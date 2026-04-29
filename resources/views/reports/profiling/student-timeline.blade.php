@extends('reports.profiling.base')
@php($title = 'Per-Student Timeline (Admin)')
@section('body')
<table class="min-w-full"><thead><tr><th class="text-left">Date</th><th class="text-left">Student</th><th class="text-left">Type</th><th class="text-left">Event</th></tr></thead><tbody>@forelse($timeline as $event)<tr><td>{{ \Illuminate\Support\Carbon::parse($event['date'])->format('Y-m-d') }}</td><td>{{ $event['student']->full_name }}</td><td>{{ $event['type'] }}</td><td>{{ $event['label'] }} ({{ $event['meta'] }})</td></tr>@empty<tr><td colspan="4">No timeline events yet.</td></tr>@endforelse</tbody></table>
@endsection
