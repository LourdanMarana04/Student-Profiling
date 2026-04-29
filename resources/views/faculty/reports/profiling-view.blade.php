@extends('faculty.reports.base')
@php($title = 'Faculty Profiling Insights')
@section('body')
<p>Focused profiling summary for assigned students.</p>
<ul><li>At-risk students: {{ $atRiskStudents->count() }}</li><li>Incomplete profiles (&lt;70%): {{ $completeness->where('percentage','<',70)->count() }}</li><li>Intervention cases: {{ $interventionItems->count() }}</li></ul>
@endsection
