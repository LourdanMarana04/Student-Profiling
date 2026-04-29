@extends('reports.profiling.base')
@php($title = 'Intervention / Case Tracking (Admin)')
@section('body')
<table class="min-w-full"><thead><tr><th class="text-left">Student</th><th class="text-left">Indicators</th><th class="text-left">Action</th></tr></thead><tbody>@forelse($interventionItems as $item)<tr><td>{{ $item['student']->full_name }}</td><td>{{ implode(', ', $item['indicators']) }}</td><td>{{ $item['recommended_action'] }}</td></tr>@empty<tr><td colspan="3">No intervention cases yet.</td></tr>@endforelse</tbody></table>
@endsection
