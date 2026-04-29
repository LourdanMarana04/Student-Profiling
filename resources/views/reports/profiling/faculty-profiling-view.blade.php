@extends('reports.profiling.base')
@php($title = 'Faculty Profiling View (Admin)')
@section('body')
<p>This view summarizes what faculty should prioritize: at-risk students, incomplete profiles, and intervention candidates.</p>
<ul><li>At-risk students: {{ $atRiskStudents->count() }}</li><li>Incomplete profiles (&lt;70%): {{ $completeness->where('percentage','<',70)->count() }}</li><li>Intervention cases: {{ $interventionItems->count() }}</li></ul>
@endsection
