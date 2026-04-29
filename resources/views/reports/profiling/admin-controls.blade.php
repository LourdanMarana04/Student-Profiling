<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Profiling Controls</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 p-3 text-green-700">{{ session('success') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border p-6">
                <h3 class="font-semibold text-lg mb-4">Risk Rule Engine</h3>
                <p class="text-sm text-gray-600 mb-4">Adjust how student risk scores are calculated. Higher weights increase influence on the final risk score.</p>
                <form action="{{ route('reports.admin-controls.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    @method('PATCH')
                    <label class="text-sm font-medium text-gray-700">Attendance Weight <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="attendance_weight" value="{{ $settings->attendance_weight }}"></label>
                    <label class="text-sm font-medium text-gray-700">Violations Weight <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="violations_weight" value="{{ $settings->violations_weight }}"></label>
                    <label class="text-sm font-medium text-gray-700">Low Grades Weight <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="low_grades_weight" value="{{ $settings->low_grades_weight }}"></label>
                    <label class="text-sm font-medium text-gray-700">Incomplete Profile Weight <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="incomplete_profile_weight" value="{{ $settings->incomplete_profile_weight }}"></label>
                    <label class="text-sm font-medium text-gray-700">Rejected Submission Weight <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="rejected_submissions_weight" value="{{ $settings->rejected_submissions_weight }}"></label>
                    <label class="text-sm font-medium text-gray-700">High Risk Threshold <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="high_risk_threshold" value="{{ $settings->high_risk_threshold }}"></label>
                    <label class="text-sm font-medium text-gray-700">Medium Risk Threshold <input class="mt-1 w-full rounded border-gray-300 focus:border-blue-500 focus:ring-blue-500" type="number" name="medium_risk_threshold" value="{{ $settings->medium_risk_threshold }}"></label>
                    <div class="md:col-span-3">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded" type="submit">Save Risk Settings</button>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <div class="text-sm text-gray-500">Pending > 3 days</div>
                    <div class="text-2xl font-bold text-amber-600">{{ $staleSubmissions }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <div class="text-sm text-gray-500">Pending > 7 days</div>
                    <div class="text-2xl font-bold text-red-600">{{ $veryStaleSubmissions }}</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <div class="text-sm text-gray-500">Unresolved Interventions</div>
                    <div class="text-2xl font-bold text-blue-700">{{ $unresolvedInterventions }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h3 class="font-semibold mb-3">Data Quality</h3>
                    <ul class="text-sm space-y-1">
                        <li>Duplicate Student IDs: <strong>{{ $duplicates->count() }}</strong></li>
                        <li>Missing Student IDs: <strong>{{ $missingStudentIds }}</strong></li>
                        <li>Missing Sections: <strong>{{ $missingSections }}</strong></li>
                        <li>Invalid Section Mapping: <strong>{{ $invalidSections }}</strong></li>
                    </ul>
                </div>
                <div class="bg-white rounded-xl shadow-sm border p-4">
                    <h3 class="font-semibold mb-3">Outcome Analytics</h3>
                    <p class="text-sm">Resolved interventions: <strong>{{ $resolvedInterventions }}</strong></p>
                    <p class="text-xs text-gray-500 mt-2">Use this alongside At-Risk reports to monitor whether follow-ups are reducing high-risk cases.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
