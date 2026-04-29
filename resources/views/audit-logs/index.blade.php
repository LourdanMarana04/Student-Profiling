<x-app-layout>
    <style>
        .page-card { background: white; border-radius: 18px; padding: 1.6rem 1.9rem; margin-bottom: 1.5rem; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); border: 1px solid #f2d1b6; }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #23150d; margin: 0 0 0.35rem; }
        .page-copy { margin: 0; color: #80553c; font-size: 0.95rem; }
        .toolbar { display: flex; gap: 0.9rem; align-items: center; flex-wrap: wrap; margin-top: 1.25rem; }
        .toolbar-input, .toolbar-select { min-width: 200px; padding: 0.85rem 1rem; border-radius: 12px; border: 1px solid #ead8cb; font-size: 0.95rem; background: #fffdfa; }
        .toolbar-button { border: none; border-radius: 999px; padding: 0.85rem 1.4rem; font-weight: 800; cursor: pointer; background: #23150d; color: white; }
        .log-card { background: white; border-radius: 18px; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); border: 1px solid #f2d1b6; overflow: hidden; }
        .log-item { padding: 1.25rem 1.5rem; border-bottom: 1px solid #f3e3d6; }
        .log-item:last-child { border-bottom: none; }
        .log-head { display: flex; justify-content: space-between; gap: 1rem; align-items: flex-start; margin-bottom: 0.6rem; }
        .log-action { display: inline-flex; align-items: center; border-radius: 999px; padding: 0.35rem 0.7rem; background: #fff1e4; color: #a24808; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.06em; }
        .log-description { color: #33231a; font-size: 0.95rem; font-weight: 700; }
        .log-meta { color: #8b6349; font-size: 0.84rem; }
        .change-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; margin-top: 0.9rem; }
        .change-box { background: #fffaf6; border: 1px solid #f3e3d6; border-radius: 14px; padding: 0.9rem 1rem; }
        .change-title { font-size: 0.8rem; font-weight: 800; text-transform: uppercase; color: #8b6349; margin-bottom: 0.55rem; }
        .change-box pre { margin: 0; white-space: pre-wrap; word-break: break-word; font-size: 0.8rem; color: #33231a; font-family: Consolas, monospace; }
        .empty-state { padding: 3rem 2rem; text-align: center; color: #8b6349; }
        .pagination { margin-top: 1.5rem; display: flex; justify-content: center; }
        @media (max-width: 768px) {
            .toolbar-input, .toolbar-select { width: 100%; min-width: 0; }
            .change-grid { grid-template-columns: 1fr; }
            .log-head { flex-direction: column; }
        }
    </style>

    <div class="page-card">
        <h1 class="page-title">Admin Audit Logs</h1>
        <p class="page-copy">Review who changed what in the admin workflows, along with before-and-after snapshots for important updates.</p>

        <form method="GET" action="{{ route('audit-logs.index') }}" class="toolbar">
            <input type="text" name="search" value="{{ old('search', $search ?? request('search')) }}" placeholder="Search description or target" class="toolbar-input" />

            <select name="action" class="toolbar-select">
                <option value="">All actions</option>
                @foreach($actions as $actionOption)
                    <option value="{{ $actionOption }}" {{ ($action ?? request('action')) === $actionOption ? 'selected' : '' }}>
                        {{ ucwords(str_replace('_', ' ', $actionOption)) }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="toolbar-button">Filter</button>
        </form>
    </div>

    <div class="log-card">
        @forelse($logs as $log)
            <div class="log-item">
                <div class="log-head">
                    <div>
                        <div class="log-action">{{ str_replace('_', ' ', $log->action) }}</div>
                        <div class="log-description" style="margin-top: 0.6rem;">{{ $log->description }}</div>
                    </div>

                    <div class="log-meta">
                        <div>{{ $log->created_at->format('M d, Y h:i A') }}</div>
                        <div>{{ $log->actor?->name ?? 'Unknown Admin' }}</div>
                        <div>{{ $log->target_type }}{{ $log->target_label ? ': '.$log->target_label : '' }}</div>
                    </div>
                </div>

                @if($log->old_values || $log->new_values)
                    <div class="change-grid">
                        <div class="change-box">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                                <div class="change-title">Before</div>
                                <div>
                                    <label style="font-size:0.8rem;color:#8b6349;margin-right:0.6rem;">View</label>
                                    <button type="button" class="toggle-json" data-target="before-{{ $log->id }}" style="border:none;background:#fff1e4;padding:6px 10px;border-radius:8px;color:#a24808;font-weight:700;">JSON</button>
                                </div>
                            </div>

                            <div id="before-{{ $log->id }}-friendly">
                                @if($log->old_values)
                                    @foreach($log->old_values as $k => $v)
                                        <div style="font-size:0.9rem;color:#33231a;margin-bottom:0.25rem;"><strong>{{ $k }}</strong>: @if(is_array($v)) {{ json_encode($v) }} @else {{ $v === null ? 'null' : $v }} @endif</div>
                                    @endforeach
                                @else
                                    <div style="color:#8b6349">No previous snapshot recorded.</div>
                                @endif
                            </div>

                            <pre id="before-{{ $log->id }}" style="display:none">{{ $log->old_values ? json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'No previous snapshot recorded.' }}</pre>
                        </div>

                        <div class="change-box">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.6rem;">
                                <div class="change-title">After</div>
                                <div>
                                    <label style="font-size:0.8rem;color:#8b6349;margin-right:0.6rem;">View</label>
                                    <button type="button" class="toggle-json" data-target="after-{{ $log->id }}" style="border:none;background:#fff1e4;padding:6px 10px;border-radius:8px;color:#a24808;font-weight:700;">JSON</button>
                                </div>
                            </div>

                            <div id="after-{{ $log->id }}-friendly">
                                @if($log->new_values)
                                    @foreach($log->new_values as $k => $v)
                                        <div style="font-size:0.9rem;color:#33231a;margin-bottom:0.25rem;"><strong>{{ $k }}</strong>: @if(is_array($v)) {{ json_encode($v) }} @else {{ $v === null ? 'null' : $v }} @endif</div>
                                    @endforeach
                                @else
                                    <div style="color:#8b6349">No new snapshot recorded.</div>
                                @endif
                            </div>

                            <pre id="after-{{ $log->id }}" style="display:none">{{ $log->new_values ? json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'No new snapshot recorded.' }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="empty-state">
                No audit logs found yet. Admin changes will appear here once they are recorded.
            </div>
        @endforelse
    </div>

    @if($logs->hasPages())
        <div class="pagination">
            {{ $logs->links() }}
        </div>
    @endif
</x-app-layout>

<script>
document.addEventListener('click', function(e){
    const btn = e.target.closest('.toggle-json');
    if(!btn) return;
    const id = btn.getAttribute('data-target');
    const pre = document.getElementById(id);
    const friendly = document.getElementById(id + '-friendly');
    if(!pre || !friendly) return;
    if(pre.style.display === 'none'){
        pre.style.display = 'block';
        friendly.style.display = 'none';
        btn.textContent = 'Friendly';
    } else {
        pre.style.display = 'none';
        friendly.style.display = 'block';
        btn.textContent = 'JSON';
    }
});
</script>
