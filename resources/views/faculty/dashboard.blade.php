@extends('layouts.app')

@section('content')
<style>
    .faculty-shell { display: grid; gap: 1.5rem; }
    .faculty-hero,
    .faculty-panel {
        background: rgba(255, 250, 245, 0.92);
        border: 1px solid rgba(201, 77, 0, 0.12);
        border-radius: 24px;
        box-shadow: 0 18px 40px rgba(133, 66, 24, 0.08);
    }
    .faculty-hero {
        padding: 1.75rem;
        background:
            radial-gradient(circle at top right, rgba(243, 106, 16, 0.18), transparent 32%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.97), rgba(255, 241, 227, 0.98));
    }
    .faculty-eyebrow {
        font-size: 0.78rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #b45309;
        font-weight: 800;
        margin-bottom: 0.45rem;
    }
    .faculty-hero h1 {
        font-size: clamp(1.8rem, 4vw, 2.5rem);
        line-height: 1.05;
        margin-bottom: 0.45rem;
        color: #1f130c;
    }
    .faculty-hero p { color: #7c5d4b; max-width: 60rem; }
    .faculty-metrics,
    .faculty-actions,
    .faculty-columns { display: grid; gap: 1rem; }
    .faculty-metrics { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
    .faculty-actions { grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); }
    .faculty-columns { grid-template-columns: 2fr 1.1fr; align-items: start; }
    .faculty-stat,
    .faculty-action {
        display: block;
        padding: 1.15rem 1.2rem;
        border-radius: 20px;
        text-decoration: none;
    }
    .faculty-stat {
        background: #fff;
        border: 1px solid rgba(201, 77, 0, 0.1);
    }
    .faculty-stat-label {
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #9a6e54;
        margin-bottom: 0.55rem;
        font-weight: 700;
    }
    .faculty-stat-value {
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        color: #1f130c;
    }
    .faculty-action {
        color: #fffaf5;
        background: linear-gradient(135deg, #f36a10, #c94d00);
        box-shadow: 0 16px 30px rgba(201, 77, 0, 0.18);
    }
    .faculty-action.is-muted {
        background: linear-gradient(135deg, #d7b7a0, #b79279);
        pointer-events: none;
        box-shadow: none;
    }
    .faculty-action-title { font-weight: 800; margin-bottom: 0.2rem; }
    .faculty-action-copy { font-size: 0.88rem; color: rgba(255, 250, 245, 0.85); }
    .faculty-panel { padding: 1.35rem; }
    .faculty-panel h2 {
        margin-bottom: 1rem;
        font-size: 1.15rem;
        color: #1f130c;
    }
    .faculty-table-wrap { overflow-x: auto; }
    .faculty-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }
    .faculty-table th,
    .faculty-table td {
        text-align: left;
        padding: 0.9rem 0.85rem;
        border-bottom: 1px solid rgba(201, 77, 0, 0.1);
    }
    .faculty-table th {
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #8b5e3c;
    }
    .faculty-table td { color: #40281a; }
    .faculty-table tr:last-child td { border-bottom: none; }
    .faculty-inline-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .faculty-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.45rem 0.75rem;
        border-radius: 999px;
        background: #fff1e3;
        color: #b45309;
        text-decoration: none;
        font-size: 0.82rem;
        font-weight: 700;
    }
    .faculty-chip.is-secondary {
        background: #e6fffb;
        color: #0f766e;
    }
    .faculty-stack { display: grid; gap: 1rem; }
    .faculty-list-item {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
        border-radius: 18px;
        background: #fff;
        border: 1px solid rgba(201, 77, 0, 0.08);
    }
    .faculty-list-title { font-weight: 700; color: #1f130c; }
    .faculty-list-copy { font-size: 0.88rem; color: #8b6a57; margin-top: 0.2rem; }
    .risk-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 0.24rem 0.65rem;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.02em;
    }
    .risk-high { background: #fee2e2; color: #991b1b; }
    .risk-medium { background: #fef3c7; color: #92400e; }
    .risk-low { background: #dcfce7; color: #166534; }
    .faculty-empty {
        padding: 1.2rem;
        border-radius: 18px;
        background: rgba(255, 255, 255, 0.7);
        color: #8b6a57;
        text-align: center;
    }
    @media (max-width: 980px) {
        .faculty-columns { grid-template-columns: 1fr; }
    }
</style>

<div class="faculty-shell">
    <section class="faculty-hero">
        <div class="faculty-eyebrow">Faculty Workspace</div>
        <h1>{{ $faculty->full_name }}</h1>
        <p>View your faculty profile context and section roster snapshots for Semester {{ $semester }}, AY {{ $academicYear }}.</p>
    </section>

    <section class="faculty-metrics">
        <article class="faculty-stat">
            <div class="faculty-stat-label">Active Courses</div>
            <div class="faculty-stat-value">{{ $currentCourses->count() }}</div>
        </article>
        <article class="faculty-stat">
            <div class="faculty-stat-label">Total Students</div>
            <div class="faculty-stat-value">{{ $assignedSections->sum(fn ($section) => $section->students->count()) }}</div>
        </article>
        <article class="faculty-stat">
            <div class="faculty-stat-label">Active Sections</div>
            <div class="faculty-stat-value">{{ $assignedSections->count() }}</div>
        </article>
        <article class="faculty-stat">
            <div class="faculty-stat-label">Open Interventions</div>
            <div class="faculty-stat-value">{{ $interventionStats['open'] ?? 0 }}</div>
        </article>
    </section>


    <section class="faculty-panel">
        <h2>Quick Actions</h2>
        <div class="faculty-actions">
            <a href="{{ route('faculty.export-cv', $faculty) }}" class="faculty-action">
                <div class="faculty-action-title">Export My CV</div>
                <div class="faculty-action-copy">Download a formatted faculty CV report in Excel.</div>
            </a>
            <a href="{{ route('faculty.reports.at-risk') }}" class="faculty-action">
                <div class="faculty-action-title">At-Risk Report</div>
                <div class="faculty-action-copy">Review students needing immediate profiling intervention.</div>
            </a>
            <a href="{{ route('faculty.reports.profile-completeness') }}" class="faculty-action">
                <div class="faculty-action-title">Profile Completeness</div>
                <div class="faculty-action-copy">Check missing profile requirements across students.</div>
            </a>
            <a href="{{ $currentCourses->isNotEmpty() && $currentCourses->first()->sections->isNotEmpty() && $currentCourses->first()->sections->first()->schedules->isNotEmpty() ? route('faculty.attendance.record', $currentCourses->first()->sections->first()->schedules->first()->id) : '#' }}" class="faculty-action {{ $currentCourses->isEmpty() || $currentCourses->first()->sections->isEmpty() || $currentCourses->first()->sections->first()->schedules->isEmpty() ? 'is-muted' : '' }}">
                <div class="faculty-action-title">Record Attendance</div>
                <div class="faculty-action-copy">Jump straight into your next available class schedule.</div>
            </a>
        </div>
    </section>

    <section class="faculty-columns">
        <article class="faculty-panel">
            <h2>Course Overview</h2>
            <div class="faculty-table-wrap">
                <table class="faculty-table">
                    <thead>
                        <tr>
                            <th>Course Code</th>
                            <th>Course Name</th>
                            <th>Sections</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseStats as $stat)
                            <tr>
                                <td>{{ $stat['code'] }}</td>
                                <td>{{ $stat['name'] }}</td>
                                <td>{{ $stat['sectionNames']->join(', ') ?: $stat['sections'] }}</td>
                                <td>{{ $stat['totalStudents'] }}</td>
                                <td>
                                    <div class="faculty-inline-actions">
                                        <a href="{{ route('faculty.courses.roster-export', $stat['id']) }}" class="faculty-chip is-secondary">Export Class List</a>
                                        @foreach($assignedSections->where('course_id', $stat['id']) as $section)
                                            <a href="{{ route('faculty.reports.section.export', $section->id) }}" class="faculty-chip">Export Section {{ $section->section_name }} Report</a>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">No courses available for this term yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        <div class="faculty-stack">
            <article class="faculty-panel">
                <h2>Assigned Sections</h2>
                @forelse($assignedSections as $section)
                    <div class="faculty-list-item">
                        <div>
                            <div class="faculty-list-title">{{ $section->section_name }}</div>
                            <div class="faculty-list-copy">
                                {{ $section->course?->course_code }} - {{ $section->course?->course_name }} |
                                {{ $section->students_count ?? $section->students->count() }} students
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="faculty-empty">No sections assigned for this term.</div>
                @endforelse
            </article>

            <article class="faculty-panel">
                <h2>Recent Announcements</h2>
                @forelse($recentAnnouncements as $announcement)
                    <div class="faculty-list-item">
                        <div>
                            <div class="faculty-list-title">{{ $announcement->subject }}</div>
                            <div class="faculty-list-copy">{{ $announcement->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="faculty-empty">No recent announcements.</div>
                @endforelse
            </article>
        </div>
    </section>

</div>
@endsection
