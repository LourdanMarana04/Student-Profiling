<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Search Results') }}
            </h2>
            <a href="{{ route('queries.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                New Search
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-2">Search Results</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Found {{ $students->total() }} student(s)
                        @if(isset($skill))
                            with skill: <strong>{{ $skill }}</strong>
                        @endif
                        @if(isset($activity))
                            with activity: <strong>{{ $activity }}</strong>
                        @endif
                    </p>
                </div>
            </div>

            @if($students->count() > 0)
                @if($format === 'table')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Year Level</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Section</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Skills</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($students as $student)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $student->student_id }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $student->full_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $student->year_level }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $student->section ?: 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($student->skills as $skill)
                                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">{{ $skill->skill_name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('students.show', $student) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">View Profile</a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                @if($format === 'cards')
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($students as $student)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="flex items-center mb-4">
                                <div class="flex-1">
                                    <h4 class="text-lg font-medium">{{ $student->full_name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->student_id }}{{ $student->section ? ' • '.$student->section : '' }}</p>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">Year {{ $student->year_level }}</span>
                            </div>

                            <div class="mb-4">
                                <h5 class="text-sm font-medium mb-2">Skills:</h5>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($student->skills as $skill)
                                    <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded text-xs">{{ $skill->skill_name }}</span>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5 class="text-sm font-medium mb-2">Activities:</h5>
                                <div class="space-y-1">
                                    @forelse($student->activities->take(2) as $activity)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity->activity_name }}</p>
                                    @empty
                                    <p class="text-sm text-gray-500 dark:text-gray-400">No activities</p>
                                    @endforelse
                                </div>
                            </div>

                            <a href="{{ route('students.show', $student) }}" class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">View Full Profile</a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($format === 'list')
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="space-y-4">
                            @foreach($students as $student)
                            <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h4 class="font-medium">{{ $student->full_name }}</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->student_id }} &bull; Year {{ $student->year_level }}{{ $student->section ? ' &bull; '.$student->section : '' }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($student->skills as $skill)
                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded text-xs">{{ $skill->skill_name }}</span>
                                        @endforeach
                                    </div>
                                </div>
                                <a href="{{ route('students.show', $student) }}" class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">View Profile</a>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-6">
                    {{ $students->appends(request()->query())->links() }}
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center text-gray-900 dark:text-gray-100">
                        <div class="mb-4">
                            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:inline-block;">
                                <circle cx="11" cy="11" r="6" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M20 20L16.65 16.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium mb-2">No students found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Try adjusting your search criteria or search for different skills/activities.</p>
                        <a href="{{ route('queries.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Try New Search</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
