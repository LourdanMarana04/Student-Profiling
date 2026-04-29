<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditSetting;
use App\Models\AuditLog;

class AuditSettingsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $redact = AuditSetting::getValue('redact', config('audit.redact'));
        if (is_array($redact)) {
            $redactList = implode("\n", $redact);
        } else {
            $redactList = is_string($redact) ? $redact : '';
        }

        $recordReads = AuditSetting::getValue('record_reads', config('audit.record_reads')) ? true : false;

        return view('admin.audit-settings', compact('redactList', 'recordReads'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $data = $request->validate([
            'redact' => 'nullable|string',
            'record_reads' => 'nullable|boolean',
        ]);

        $oldRedact = AuditSetting::getValue('redact', config('audit.redact'));
        $oldRecordReads = AuditSetting::getValue('record_reads', config('audit.record_reads')) ? true : false;

        $newRedact = array_filter(array_map('trim', explode("\n", $data['redact'] ?? '')));
        AuditSetting::setValue('redact', $newRedact, auth()->id());
        AuditSetting::setValue('record_reads', $request->boolean('record_reads'), auth()->id());

        $newRecordReads = $request->boolean('record_reads');

        AuditLog::recordIfAdmin(
            'audit_settings.updated',
            'audit_settings',
            null,
            null,
            'Updated audit settings',
            ['redact' => $oldRedact, 'record_reads' => $oldRecordReads],
            ['redact' => $newRedact, 'record_reads' => $newRecordReads]
        );

        return redirect()->route('admin.audit-settings')->with('status', 'Audit settings updated.');
    }
}
