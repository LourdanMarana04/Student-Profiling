<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditLog;

class VerifyAuditLogsIntegrity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'verify:audit-logs {--verbose}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify the tamper-evident integrity of the audit log chain';

    public function handle()
    {
        $this->info('Starting audit log integrity verification...');

        $result = AuditLog::verifyIntegrity();

        if ($result['ok']) {
            $this->info('OK — no integrity errors found.');
            return 0;
        }

        $this->error("Found {$result['errors']} integrity errors:");

        foreach ($result['details'] as $d) {
            $this->line(' - ' . $d);
        }

        return 2;
    }
}
