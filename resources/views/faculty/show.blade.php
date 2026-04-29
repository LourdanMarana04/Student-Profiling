<x-app-layout>
    @php
        $canManageFaculty = auth()->user()->canManageFaculty();
        $isFacultyMember = $faculty->user_id === auth()->id();
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Faculty Profile: ') . $faculty->full_name }}
            </h2>
            <div>
                @if($canManageFaculty || $isFacultyMember)
                    <a href="{{ route('faculty.edit', $faculty) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edit Profile
                    </a>
                    <a href="{{ route('faculty.export-cv', $faculty) }}" class="bg-emerald-500 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Export CV
                    </a>
                    <a href="{{ route('faculty.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Back to List
                    </a>
                @else
                    <a href="{{ route('profile.edit') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Account Settings
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Basic Information -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <strong>Faculty ID:</strong> {{ $faculty->faculty_id }}
                        </div>
                        <div>
                            <strong>Full Name:</strong> {{ $faculty->full_name }}
                        </div>
                        <div>
                            <strong>Email:</strong> {{ $faculty->email }}
                        </div>
                        <div>
                            <strong>Phone:</strong> {{ $faculty->phone ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Department:</strong> {{ $faculty->department?->name ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Office:</strong> {{ $faculty->office ?? 'N/A' }}
                        </div>
                        @if($faculty->specialization)
                        <div class="col-span-2">
                            <strong>Specialization:</strong> {{ $faculty->specialization }}
                        </div>
                        @endif
                        <div>
                            <strong>Status:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($faculty->status === 'active') bg-green-100 text-green-800
                                @elseif($faculty->status === 'inactive') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($faculty->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teaching Courses -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">
                        Courses Teaching (Semester {{ $semester }}, Academic Year {{ $academicYear }})
                    </h3>
                    @if(!empty($teachingFallback) && $teachingFallback)
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">No courses assigned for Semester {{ $semester }}, AY {{ $academicYear }} — showing all assigned courses across terms.</p>
                    @endif
                    @if($teachingCourses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Credits</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Department</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($teachingCourses as $course)
                                    <tr>
                                        <td class="px-4 py-2 text-sm">{{ $course->course_code }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $course->course_name }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $course->credits }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $course->department?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($course->status === 'active') bg-green-100 text-green-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ ucfirst($course->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">
                            No courses assigned for this semester.
                        </p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Assigned Sections</h3>
                    @if(!empty($sectionsFallback) && $sectionsFallback)
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">No sections assigned for Semester {{ $semester }}, showing all sections assigned to this faculty across terms.</p>
                    @endif
                    @if($assignedSections->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Section</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course Code</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Year Level</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Students</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($assignedSections as $section)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $section->section_name }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $section->course?->course_name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $section->course?->course_code ?? 'N/A' }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $section->year_level }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $section->students->count() }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No sections are assigned for this semester.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">Assigned Students</h3>
                    @if(!empty($studentsFallback) && $studentsFallback)
                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">No students were found for the current semester; showing students assigned to this faculty across all sections/terms.</p>
                    @endif
                    @if($assignedStudents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student ID</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Student Name</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Sections</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($assignedStudents as $student)
                                        <tr>
                                            <td class="px-4 py-2 text-sm">{{ $student->student_id }}</td>
                                            <td class="px-4 py-2 text-sm">{{ $student->full_name }}</td>
                                            <td class="px-4 py-2 text-sm">
                                                {{ $student->sections->where('faculty_id', $faculty->id)->pluck('section_name')->join(', ') ?: 'N/A' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No students are currently assigned to this faculty member.</p>
                    @endif
                </div>
            </div>

            <!-- All Course Assignments -->
            @if(isset($allAssignedCourses) && $allAssignedCourses->count() > 0)
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-medium mb-4">All Course Assignments</h3>
                    @foreach($allAssignedCourses as $year => $semesters)
                        <div class="mb-6">
                            <h4 class="text-md font-semibold mb-3 text-gray-700 dark:text-gray-300">Academic Year {{ $year }}</h4>
                            @foreach($semesters as $sem => $courses)
                                <div class="mb-4 ml-4">
                                    <h5 class="text-sm font-medium mb-2 text-gray-600 dark:text-gray-400">Semester {{ $sem }}</h5>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full table-auto">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course Code</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Course Name</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Credits</th>
                                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Department</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach($courses as $course)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm">{{ $course->course_code }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $course->course_name }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $course->credits }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $course->department?->name ?? 'N/A' }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
