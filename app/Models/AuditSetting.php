<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditSetting extends Model
{
    protected $table = 'audit_settings';

    protected $fillable = ['key', 'value', 'updated_by'];

    public $casts = [
        'value' => 'string',
    ];

    public static function getValue(string $key, $default = null)
    {
        $rec = self::where('key', $key)->first();
        if (! $rec) return $default;

        // try to decode JSON
        $v = $rec->value;
        $decoded = json_decode($v, true);
        return json_last_error() === JSON_ERROR_NONE ? $decoded : $v;
    }

    public static function setValue(string $key, $value, ?int $updatedBy = null): self
    {
        $rec = self::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : (string) $value, 'updated_by' => $updatedBy]);
        return $rec;
    }
}
