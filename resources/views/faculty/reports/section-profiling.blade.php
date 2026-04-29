@extends('faculty.reports.base')
@php($title = 'Section Profiling Report: ' . $section->section_name)
@section('body')
<div class="mb-4">
    <a href="{{ route('faculty.reports.section.export', $section->id) }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
        Export This Section to Excel
    </a>
    <p class="mt-2 text-sm text-gray-600">The Excel file includes completion %, intervention status, indicators, and recommended support action per student.</p>
</div>
<p class="mb-4">Course: {{ $section->course?->course_code }} - {{ $section->course?->course_name }}</p>
<div class="mb-4"><strong>Students in section:</strong> {{ $students->count() }} | <strong>At-risk:</strong> {{ $atRiskStudents->count() }}</div>
<table class="min-w-full"><thead><tr><th class="text-left">Student</th><th class="text-left">Completion</th><th class="text-left">Intervention</th></tr></thead><tbody>@foreach($completeness as $row)<tr><td>{{ $row['student']->full_name }}</td><td>{{ $row['percentage'] }}%</td><td>{{ $interventionItems->contains(fn ($item) => (int) $item['student']->id === (int) $row['student']->id) ? 'needs attention' : 'stable' }}</td></tr>@endforeach</tbody></table>
@endsection
