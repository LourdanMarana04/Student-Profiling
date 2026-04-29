<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ __('Faculty Reports') }}</h2>
            <a href="{{ route('reports.faculty.export') }}" class="inline-flex items-center rounded-lg border border-blue-600 px-4 py-2 text-sm font-semibold text-blue-900 shadow-sm hover:bg-blue-200" style="background-color:#dbeafe;">
                Export to Excel
            </a>
        </div>
    </x-slot>

    <style>
        .report-shell { max-width: 1280px; margin: 0 auto; padding: 1.5rem; }
        .report-hero {
            border-radius: 18px;
            padding: 1.25rem 1.5rem;
            color: #e2e8f0;
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            box-shadow: 0 16px 30px rgba(15, 23, 42, 0.24);
        }
        .report-eyebrow { font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.08em; color: #bfdbfe; font-weight: 700; }
        .report-title { font-size: 1.5rem; font-weight: 800; margin-top: 0.35rem; color: #ffffff; }
        .report-copy { margin-top: 0.45rem; color: #dbeafe; font-size: 0.95rem; }
        .report-stats { margin-top: 1rem; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.75rem; }
        .report-stat { background: rgba(255,255,255,0.12); border: 1px solid rgba(191,219,254,0.35); border-radius: 12px; padding: 0.75rem; }
        .report-stat-label { font-size: 0.76rem; text-transform: uppercase; letter-spacing: 0.05em; color: #bfdbfe; font-weight: 700; }
        .report-stat-value { margin-top: 0.2rem; font-size: 1.35rem; line-height: 1; font-weight: 800; color: #fff; }
        .report-card { margin-top: 1rem; border-radius: 18px; background: #fff; border: 1px solid #e2e8f0; box-shadow: 0 8px 20px rgba(15,23,42,0.06); overflow: hidden; }
        .report-table-wrap { overflow-x: auto; }
        .report-table { width: 100%; min-width: 1100px; border-collapse: collapse; }
        .report-table th { background: #eff6ff; color: #1e3a8a; font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 800; text-align: left; padding: 0.85rem; border-bottom: 1px solid #bfdbfe; }
        .report-table td { padding: 0.85rem; border-bottom: 1px solid #e2e8f0; color: #0f172a; vertical-align: top; font-size: 0.88rem; }
        .report-table tbody tr:nth-child(even) td { background: #f8fafc; }
        .status-badge { display: inline-flex; border-radius: 999px; padding: 0.3rem 0.65rem; font-size: 0.75rem; font-weight: 700; text-transform: capitalize; }
        .status-active { background: #dcfce7; color: #166534; }
        .status-inactive { background: #fee2e2; color: #991b1b; }
        .status-on_leave { background: #fef3c7; color: #92400e; }
        .mini-btn { display: inline-flex; border-radius: 8px; padding: 0.45rem 0.7rem; font-size: 0.78rem; font-weight: 700; text-decoration: none; background: #1d4ed8; color: #fff; }
        .mini-btn:hover { background: #1e40af; }
        .muted { color: #64748b; }
        @media (max-width: 900px) { .report-stats { grid-template-columns: 1fr; } }
    </style>

    <div class="report-shell">
        <a href="{{ route('reports') }}" class="inline-flex items-center rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-800 hover:bg-gray-300 mb-4">&larr; Back to Reports</a>
        <section class="report-hero">
            <div class="report-eyebrow">System Admin</div>
            <div class="report-title">Faculty Reports</div>
            <div class="report-copy">Review faculty records, teaching assignments, and generate report exports from one place.</div>
            <div class="report-stats">
                <div class="report-stat">
                    <div class="report-stat-label">Faculty Records</div>
                    <div class="report-stat-value">{{ $faculty->count() }}</div>
                </div>
                <div class="report-stat">
                    <div class="report-stat-label">Section Tracking</div>
                    <div class="report-stat-value">{{ $supportsSectionAssignments ? 'Enabled' : 'Disabled' }}</div>
                </div>
                <div class="report-stat">
                    <div class="report-stat-label">Export Ready</div>
                    <div class="report-stat-value">Excel</div>
                </div>
            </div>
        </section>

        <section class="report-card">
            <div class="report-table-wrap">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th>Faculty ID</th>
                            <th>Faculty Name</th>
                            <th>Department</th>
                            <th>Email</th>
                            <th>Specialization</th>
                            <th>Courses</th>
                            <th>Sections</th>
                            <th>Status</th>
                            <th>CV Export</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faculty as $member)
                            <tr>
                                <td><strong>{{ $member->faculty_id ?: $member->id }}</strong></td>
                                <td>{{ $member->full_name ?: $member->user->name }}</td>
                                <td>{{ $member->department->name ?? 'N/A' }}</td>
                                <td>{{ $member->email ?: $member->user->email }}</td>
                                <td class="muted">{{ $member->specialization ?: 'N/A' }}</td>
                                <td class="muted">{{ $member->courses->pluck('course_name')->join(', ') ?: 'None' }}</td>
                                <td class="muted">{{ $supportsSectionAssignments ? ($member->sections->pluck('section_name')->join(', ') ?: 'None') : 'Not available' }}</td>
                                <td>
                                    <span class="status-badge status-{{ $member->status }}">{{ str_replace('_', ' ', $member->status) }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('faculty.export-cv', $member) }}" class="mini-btn">Export CV</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="muted">No faculty records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</x-app-layout>
