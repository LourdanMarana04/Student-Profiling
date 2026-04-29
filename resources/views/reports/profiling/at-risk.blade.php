@extends('reports.profiling.base')
@php($title = 'At-Risk Report (Admin)')
@section('body')
<table class="min-w-full"><thead><tr><th class="text-left">Student</th><th class="text-left">Risk</th><th class="text-left">Completion</th></tr></thead><tbody>@forelse($atRiskStudents as $s)<tr><td>{{ $s['student']->full_name }}</td><td>{{ strtoupper($s['risk_level']) }} ({{ $s['risk_score'] }})</td><td>{{ $s['student']->profileCompletionPercentage() }}%</td></tr>@empty<tr><td colspan="3">No at-risk students found.</td></tr>@endforelse</tbody></table>
@endsection
