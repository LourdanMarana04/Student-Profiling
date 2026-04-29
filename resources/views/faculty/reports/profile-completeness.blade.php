@extends('faculty.reports.base')
@php($title = 'Faculty Profile Completeness Report')
@section('body')
<table class="min-w-full"><thead><tr><th class="text-left">Student</th><th class="text-left">Score</th><th class="text-left">Missing</th></tr></thead><tbody>@foreach($completeness as $row)<tr><td>{{ $row['student']->full_name }}</td><td>{{ $row['percentage'] }}%</td><td>{{ implode(', ', $row['missing']) ?: 'Complete' }}</td></tr>@endforeach</tbody></table>
@endsection
