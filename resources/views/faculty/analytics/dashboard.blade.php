@extends('layouts.app')

@section('content')
<style>
    .analytics-shell { display: grid; gap: 1.5rem; }
    .analytics-hero,
    .analytics-panel {
        border-radius: 24px;
        border: 1px solid rgba(201, 77, 0, 0.12);
        background: rgba(255, 250, 245, 0.94);
        box-shadow: 0 18px 38px rgba(125, 66, 22, 0.08);
    }
    .analytics-hero {
        padding: 1.75rem;
        background:
            radial-gradient(circle at top right, rgba(243, 106, 16, 0.2), transparent 35%),
            linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(255, 241, 227, 0.96));
    }
    .analytics-hero h1 {
        font-size: clamp(1.75rem, 4vw, 2.4rem);
        color: #1f130c;
        margin-bottom: 0.35rem;
    }
    .analytics-hero p { color: #8b6a57; max-width: 54rem; }
    .analytics-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
    }
    .analytics-stat {
        padding: 1.2rem;
        border-radius: 20px;
        background: #fff;
        border: 1px solid rgba(201, 77, 0, 0.1);
    }
    .analytics-stat-label {
        font-size: 0.82rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #9a6e54;
        margin-bottom: 0.55rem;
    }
    .analytics-stat-value {
        font-size: 2rem;
        line-height: 1;
        font-weight: 800;
        color: #1f130c;
    }
    .analytics-panel { padding: 1.35rem; }
    .analytics-panel h2 {
        margin-bottom: 1rem;
        color: #1f130c;
        font-size: 1.15rem;
    }
    .analytics-table-wrap { overflow-x: auto; }
    .analytics-table {
        width: 100%;
        min-width: 760px;
        border-collapse: collapse;
    }
    .analytics-table th,
    .analytics-table td {
        padding: 0.9rem 0.85rem;
        text-align: left;
        border-bottom: 1px solid rgba(201, 77, 0, 0.1);
    }
    .analytics-table th {
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #8b5e3c;
    }
    .analytics-table td { color: #40281a; }
    .analytics-table tr:last-child td { border-bottom: none; }
    .analytics-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
    }
    .analytics-link,
    .analytics-button {
        border: none;
        border-radius: 999px;
        padding: 0.7rem 1rem;
        font-size: 0.88rem;
        font-weight: 800;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .analytics-link {
        background: linear-gradient(135deg, #f36a10, #c94d00);
        color: #fffaf5;
        box-shadow: 0 16px 30px rgba(201, 77, 0, 0.18);
    }
    .analytics-button {
        background: #fff1e3;
        color: #b45309;
    }
    .analytics-empty {
        padding: 1.15rem;
        text-align: center;
        color: #8b6a57;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 18px;
    }
</style>

<div class="analytics-shell">
    <section class="analytics-hero">
        <h1>Teaching Analytics Dashboard</h1>
        <p>Review attendance, grades, engagement, and completion trends across your sections with a clearer faculty-facing overview.</p>
    </section>

    <section class="analytics-summary">
        <article class="analytics-stat">
            <div class="analytics-stat-label">Avg Attendance Rate</div>
            <div class="analytics-stat-value">{{ number_format($summary['avgAttendanceRate'], 1) }}%</div>
        </article>
        <article class="analytics-stat">
            <div class="analytics-stat-label">Avg Student Grade</div>
            <div class="analytics-stat-value">{{ number_format($summary['avgStudentGrade'], 1) }}</div>
        </article>
        <article class="analytics-stat">
            <div class="analytics-stat-label">Avg Engagement</div>
            <div class="analytics-stat-value">{{ number_format($summary['avgEngagement'], 1) }}%</div>
        </article>
        <article class="analytics-stat">
            <div class="analytics-stat-label">Total Courses</div>
            <div class="analytics-stat-value">{{ $summary['totalCourses'] }}</div>
        </article>
    </section>

    <section class="analytics-panel">
        <h2>Course Performance</h2>
        <div class="analytics-table-wrap">
            <table class="analytics-table">
                <thead>
                    <tr>
                        <th>Course</th>
                        <th>Section</th>
                        <th>Students</th>
                        <th>Attendance</th>
                        <th>Avg Grade</th>
                        <th>Engagement</th>
                        <th>Completion</th>
                        <th>Insights</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($analyticsData as $analytics)
                        <tr>
                            <td>{{ $analytics->course->course_name }}</td>
                            <td>{{ $analytics->section->section_name }}</td>
                            <td>{{ $analytics->total_students }}</td>
                            <td>{{ number_format($analytics->avg_attendance_rate, 1) }}%</td>
                            <td>{{ number_format($analytics->avg_class_grade, 1) }}</td>
                            <td>{{ number_format($analytics->student_engagement_score, 1) }}%</td>
                            <td>{{ number_format($analytics->assignment_completion_rate, 1) }}%</td>
                            <td>
                                <button type="button" class="analytics-button" onclick="showInsights({{ $analytics->section_id }})">
                                    View Insights
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="analytics-empty">No analytics data available.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="analytics-actions">
        <a href="{{ route('faculty.analytics.curriculum') }}" class="analytics-link">Curriculum Insights</a>
        <a href="{{ route('faculty.analytics.engagement') }}" class="analytics-link">Student Engagement</a>
    </section>
</div>

<script>
function showInsights(sectionId) {
    fetch(`/faculty/analytics/insights/${sectionId}`)
        .then(response => response.json())
        .then(data => {
            alert(JSON.stringify(data, null, 2));
        });
}
</script>
@endsection
