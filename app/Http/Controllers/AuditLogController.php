<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(auth()->user()?->isAdmin(), Response::HTTP_FORBIDDEN);

        $search = $request->query('search');
        $action = $request->query('action');

        $logs = AuditLog::with('actor')
            ->when($search, function ($query, $search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('description', 'like', "%{$search}%")
                        ->orWhere('target_label', 'like', "%{$search}%")
                        ->orWhere('target_type', 'like', "%{$search}%");
                });
            })
            ->when($action, fn ($query, $action) => $query->where('action', $action))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('audit-logs.index', [
            'logs' => $logs,
            'search' => $search,
            'action' => $action,
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
