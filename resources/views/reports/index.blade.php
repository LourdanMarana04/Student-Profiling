<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports') }}
        </h2>
    </x-slot>

    <style>
        .report-card { transition: transform .2s ease, box-shadow .2s ease; }
        .report-card:hover { transform: translateY(-2px); box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08); }
        .btn-view { background: #2563eb; }
        .btn-view:hover { background: #1d4ed8; }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="mb-6 rounded-xl border border-blue-100 bg-blue-50 p-4">
                        <h3 class="text-lg font-semibold text-blue-900">Available Reports</h3>
                        <p class="text-sm text-blue-800 mt-1">Use these reports to monitor student profiling quality, risk trends, intervention outcomes, and data integrity.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Students Report</h4>
                            <p class="text-sm text-gray-600 mb-4">View and export student data including profiles, departments, and curricula.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.students') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                                <a href="{{ route('reports.students.export') }}" class="font-bold py-2 px-4 rounded text-sm border" style="background-color:#d1fae5;color:#14532d;border-color:#34d399;">Export Excel</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Faculty Report</h4>
                            <p class="text-sm text-gray-600 mb-4">View and export faculty data including profiles, departments, and assigned courses.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.faculty') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                                <a href="{{ route('reports.faculty.export') }}" class="font-bold py-2 px-4 rounded text-sm border" style="background-color:#d1fae5;color:#14532d;border-color:#34d399;">Export Excel</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Departments Report</h4>
                            <p class="text-sm text-gray-600 mb-4">View department information including associated students and faculty.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.departments') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                                <a href="{{ route('reports.departments.export') }}" class="font-bold py-2 px-4 rounded text-sm border" style="background-color:#d1fae5;color:#14532d;border-color:#34d399;">Export Excel</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Courses Report</h4>
                            <p class="text-sm text-gray-600 mb-4">View course information including assigned faculty and curricula.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.courses') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">At-Risk Report</h4>
                            <p class="text-sm text-gray-600 mb-4">Flags students using attendance and violations, plus profile risk indicators.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.at-risk') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Profile Completeness</h4>
                            <p class="text-sm text-gray-600 mb-4">Shows completion score and missing critical profile fields per student.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.profile-completeness') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Intervention Tracking</h4>
                            <p class="text-sm text-gray-600 mb-4">Tracks student risk indicators with recommended adviser interventions.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.interventions') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Student Timeline</h4>
                            <p class="text-sm text-gray-600 mb-4">Chronological student profile events for review and case context.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.student-timeline') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>

                        <div class="report-card bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <h4 class="font-semibold mb-2">Admin Controls</h4>
                            <p class="text-sm text-gray-600 mb-4">Risk scoring rules, SLA tracking, data quality checks, and outcome monitoring.</p>
                            <div class="flex space-x-2">
                                <a href="{{ route('reports.admin-controls') }}" class="btn-view text-white font-bold py-2 px-4 rounded text-sm">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
