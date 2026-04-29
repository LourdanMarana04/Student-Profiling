<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;

/**
 * Middleware to record read/access events as audit logs.
 *
 * Note: register this middleware in `app/Http/Kernel.php` or apply to routes
 * where read auditing is required (e.g., student/profile show routes).
 */
class LogReadAccess
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            if (! config('audit.record_reads', true)) {
                return $response;
            }

            // Only record GET requests that return a 200 status
            if ($request->isMethod('get') && $response->getStatusCode() === 200) {
                $actor = $request->user();
                $path = $request->path();

                AuditLog::recordIfAdmin(
                    'record_read',
                    'read',
                    null,
                    $path,
                    "Read access to {$path}",
                    null,
                    null
                );
            }
        } catch (\Throwable $_) {
            // don't interrupt the request on audit failures
        }

        return $response;
    }
}
