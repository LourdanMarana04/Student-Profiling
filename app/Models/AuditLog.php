<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use function json_encode;

class AuditLog extends Model
{
    protected $fillable = [
        'actor_user_id',
        'action',
        'target_type',
        'target_id',
        'target_label',
        'description',
        'old_values',
        'new_values',
        'previous_hash',
        'record_hash',
        'ip_address',
        'user_agent',
        'request_path',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'context' => 'array',
        ];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }

    /**
     * @param  array<string, mixed>|null  $oldValues
     * @param  array<string, mixed>|null  $newValues
     */
    public static function recordIfAdmin(
        string $action,
        string $targetType,
        ?int $targetId,
        ?string $targetLabel,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        $actor = Auth::user();

        // sanitize values according to config before storing
        $old = $oldValues ? self::sanitizeSnapshot($oldValues) : null;
        $new = $newValues ? self::sanitizeSnapshot($newValues) : null;

        self::create([
            'actor_user_id' => $actor?->id,
            'action' => $action,
            'target_type' => $targetType,
            'target_id' => $targetId,
            'target_label' => $targetLabel,
            'description' => $description,
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }

    /**
     * Sanitize a snapshot array by redacting configured keys.
     * Recurses into nested arrays.
     *
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    public static function sanitizeSnapshot(array $data): array
    {
        $placeholder = config('audit.redaction_placeholder', '[REDACTED]');

        // Prefer DB-backed settings when available
        $dbRedact = null;
        try {
            $dbRedact = \App\Models\AuditSetting::getValue('redact', null);
        } catch (\Throwable $_) {
            $dbRedact = null;
        }

        $redact = is_array($dbRedact) ? $dbRedact : (array) config('audit.redact', []);

        $sanitize = function ($value) use (&$sanitize, $redact, $placeholder) {
            if (is_array($value)) {
                $out = [];
                foreach ($value as $k => $v) {
                    $out[$k] = $sanitize($v);
                }
                return $out;
            }

            return $value;
        };

        $result = [];
        foreach ($data as $k => $v) {
            if (in_array(strtolower($k), array_map('strtolower', $redact), true)) {
                $result[$k] = $placeholder;
                continue;
            }

            if (is_array($v)) {
                $result[$k] = $sanitize($v);
            } else {
                $result[$k] = $v;
            }
        }

        return $result;
    }

    /**
     * Compute hash chain and capture request metadata.
     */
    protected static function booted()
    {
        static::creating(function (self $model) {
            try {
                $model->ip_address = request()->ip() ?? null;
                $model->user_agent = mb_substr(request()->userAgent() ?? '', 0, 2000);
                $model->request_path = request()->path() ?? null;
                $model->context = null;
            } catch (\Throwable $_) {
                // In console / non-http contexts the request() helper may not be available.
            }

            $previous = self::orderBy('id', 'desc')->value('record_hash');
            $model->previous_hash = $previous;
        });

        static::created(function (self $model) {
            $data = [
                'actor_user_id' => $model->actor_user_id,
                'action' => $model->action,
                'target_type' => $model->target_type,
                'target_id' => $model->target_id,
                'target_label' => $model->target_label,
                'description' => $model->description,
                'old_values' => $model->old_values,
                'new_values' => $model->new_values,
                'ip_address' => $model->ip_address,
                'user_agent' => $model->user_agent,
                'request_path' => $model->request_path,
                'previous_hash' => $model->previous_hash,
                'created_at' => $model->created_at?->toIsoString(),
            ];

            $secret = Config::get('app.key') ?? env('APP_KEY');
            $hash = hash_hmac('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $secret);

            // Save the computed record hash without firing events.
            $model->record_hash = $hash;
            $model->saveQuietly();
        });
    }

    /**
     * Verify the integrity of the audit log chain. Returns an array with
     * keys: ok (bool), errors (int), details (array of messages).
     */
    public static function verifyIntegrity(): array
    {
        $secret = Config::get('app.key') ?? env('APP_KEY');
        $errors = 0;
        $details = [];
        $prevHash = null;

        foreach (self::orderBy('id')->cursor() as $log) {
            $data = [
                'actor_user_id' => $log->actor_user_id,
                'action' => $log->action,
                'target_type' => $log->target_type,
                'target_id' => $log->target_id,
                'target_label' => $log->target_label,
                'description' => $log->description,
                'old_values' => $log->old_values,
                'new_values' => $log->new_values,
                'ip_address' => $log->ip_address,
                'user_agent' => $log->user_agent,
                'request_path' => $log->request_path,
                'previous_hash' => $log->previous_hash,
                'created_at' => $log->created_at?->toIsoString(),
            ];

            $expected = hash_hmac('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $secret);

            if ($log->record_hash !== $expected) {
                $errors++;
                $details[] = "Mismatch hash for id={$log->id}";
            }

            if ($prevHash !== null && $log->previous_hash !== $prevHash) {
                $errors++;
                $details[] = "Broken chain at id={$log->id} (prev_hash mismatch)";
            }

            $prevHash = $log->record_hash;
        }

        return ['ok' => $errors === 0, 'errors' => $errors, 'details' => $details];
    }
}
