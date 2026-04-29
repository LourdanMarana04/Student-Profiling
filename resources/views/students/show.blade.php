<x-app-layout>
    @php
        $canManageStudents = auth()->user()->canManageStudents();
        $isOwnProfile = $student->user_id === auth()->id();
        $completion = $student->profileCompletionPercentage();
        $missingItems = $student->incompleteProfileItems();

        $approvalBadgeClasses = [
            'approved' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'rejected' => 'bg-red-100 text-red-800',
        ];
    @endphp

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-black-200 leading-tight">
                {{ __('Student Profile: ') . $student->full_name }}
            </h2>
            <div>
                @if($canManageStudents)
                    <a href="{{ route('students.edit', $student) }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edit Student
                    </a>
                    <a href="{{ route('students.index') }}" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

    <style>
        .profile-panel { border: 1px solid #f3ddcc; border-radius: 16px; box-shadow: 0 12px 28px rgba(15, 23, 42, 0.05); }
        .section-title { font-weight: 700; color: #1f2937; }
        .status-pill { display: inline-flex; align-items: center; border-radius: 999px; padding: 0.2rem 0.65rem; font-size: 0.72rem; font-weight: 700; }
        .risk-high { background: #fee2e2; color: #991b1b; }
        .risk-medium { background: #fef3c7; color: #92400e; }
        .risk-low { background: #dcfce7; color: #166534; }
        .case-chip { border-radius: 999px; padding: 0.18rem 0.6rem; font-size: 0.72rem; font-weight: 700; }
        .case-open { background: #fef3c7; color: #92400e; }
        .case-progress { background: #dbeafe; color: #1e40af; }
        .case-resolved { background: #dcfce7; color: #166534; }
        .submission-form {
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 1rem;
            background: #fff;
        }
        .submission-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 0.3rem;
        }
        .submission-input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #d1d5db;
            padding: 0.62rem 0.72rem;
            font-size: 0.92rem;
            line-height: 1.25rem;
            color: #111827;
            background: #fff;
        }
        .submission-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.16);
            outline: none;
        }
        .admin-form-card {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            padding: 1rem;
        }
        .admin-field-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #4b5563;
            margin-bottom: 0.3rem;
        }
        .admin-field-input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            padding: 0.6rem 0.7rem;
            font-size: 0.92rem;
            color: #111827;
            background: #fff;
        }
        .admin-field-input:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.16);
            outline: none;
        }
        .record-shell {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            background: #fff;
            padding: 1rem;
        }
        .record-title {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }
        .record-meta {
            margin-top: 0.35rem;
            font-size: 0.86rem;
            color: #4b5563;
            line-height: 1.35rem;
        }
        .cta-submit-btn {
            background: #f97316;
            color: #111827 !important;
            border-radius: 10px;
            padding: 0.65rem 1.15rem;
            font-size: 0.9rem;
            font-weight: 700;
        }
        .cta-submit-btn:hover {
            background: #ea580c;
            color: #ffffff !important;
        }
    </style>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($isOwnProfile && session('registration_success_message'))
                <div
                    x-data="{ open: true }"
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                    style="display: none;"
                >
                    <div
                        @click.outside="open = false"
                        class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl"
                    >
                        <div class="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-orange-600">Student Registration</div>
                        <h3 class="text-xl font-bold text-gray-900">Registration successful</h3>
                        <p class="mt-3 text-sm leading-6 text-gray-600">
                            {{ session('registration_success_message') }}
                        </p>
                        <div class="mt-6 flex justify-end">
                            <button
                                type="button"
                                @click="open = false"
                                class="rounded-full bg-orange-500 px-5 py-2 text-sm font-semibold text-white hover:bg-orange-600"
                            >
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if($isOwnProfile)
                <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white text-gray-900">
                        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-lg section-title">Profile Completion Tracker</h3>
                                <p class="text-sm text-gray-600">Complete more of your student profile to keep your record accurate and useful.</p>
                            </div>
                            <div class="text-2xl font-bold text-orange-600">{{ $completion }}%</div>
                        </div>

                        <div class="mt-4 h-3 w-full overflow-hidden rounded-full bg-gray-200">
                            <div class="h-full rounded-full bg-orange-500 transition-all" style="width: {{ $completion }}%;"></div>
                        </div>

                        @if(count($missingItems) > 0)
                            <div class="mt-4">
                                <p class="text-sm font-medium text-gray-800">Incomplete items</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach($missingItems as $item)
                                        <span class="rounded-full bg-orange-100 px-3 py-1 text-sm text-orange-700">
                                            {{ $item }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="mt-4 text-sm font-medium text-green-700">Your profile is fully complete.</p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <h3 class="text-lg section-title mb-3">Student Success Case File</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="rounded border p-3">
                            <div class="text-xs text-gray-500 uppercase">Risk Score</div>
                            <div class="text-2xl font-bold">{{ $risk['score'] }}</div>
                            <div class="text-sm capitalize mt-1">
                                <span class="status-pill risk-{{ $risk['level'] }}">{{ strtoupper($risk['level']) }} RISK</span>
                            </div>
                        </div>
                        <div class="rounded border p-3 md:col-span-2">
                            <div class="text-xs text-gray-500 uppercase">Student Insight</div>
                            <div class="text-sm mt-1">{{ $insight }}</div>
                            @if(!empty($risk['reasons']))
                                <div class="text-xs text-gray-600 mt-2">Indicators: {{ implode(', ', $risk['reasons']) }}</div>
                            @endif
                        </div>
                    </div>
                    @if($isOwnProfile)
                        <div class="mt-4">
                            <h4 class="font-medium text-sm mb-2">Profile Roadmap - Next Best Actions</h4>
                            <div class="flex flex-wrap gap-2">
                                @forelse($roadmapActions as $action)
                                    <span class="rounded-full bg-orange-100 text-orange-800 px-3 py-1 text-xs">{{ $action }}</span>
                                @empty
                                    <span class="rounded-full bg-green-100 text-green-800 px-3 py-1 text-xs">Great job. Your roadmap is complete.</span>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Basic Information -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <h3 class="text-lg section-title mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <strong>Student ID:</strong> {{ $student->student_id }}
                        </div>
                        <div>
                            <strong>Full Name:</strong> {{ $student->full_name }}
                        </div>
                        <div>
                            <strong>Year Level:</strong> {{ $student->year_level }}
                        </div>
                        <div>
                            <strong>Section:</strong> {{ $student->section ?: 'N/A' }}
                        </div>
                        <div>
                            <strong>Email:</strong> {{ $student->email }}
                        </div>
                        <div>
                            <strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                @if($student->status === 'active') bg-green-100 text-green-800
                                @elseif($student->status === 'inactive') bg-yellow-100 text-yellow-800
                                @elseif($student->status === 'graduated') bg-blue-100 text-blue-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($student->status) }}
                            </span>
                        </div>
                        @if($student->date_of_birth)
                        <div>
                            <strong>Date of Birth:</strong> {{ $student->date_of_birth->format('M d, Y') }}
                        </div>
                        @endif
                        @if($student->gender)
                        <div>
                            <strong>Gender:</strong> {{ ucfirst($student->gender) }}
                        </div>
                        @endif
                        @if($student->address)
                        <div class="col-span-2">
                            <strong>Address:</strong> {{ $student->address }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic History -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-4">
                        <h3 class="text-lg section-title">Academic History</h3>
                        @if($canManageStudents)
                            <div class="space-y-2">
                                <p class="text-sm text-gray-600">Assign a curriculum to this student and all curriculum subjects will be added to academic history.</p>
                            </div>
                        @endif
                    </div>

                    @if($canManageStudents)
                        <form action="{{ route('students.assign-curriculum', $student) }}" method="POST" class="grid gap-4 mb-6 lg:grid-cols-5">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Department</label>
                                <select name="department_id" class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" class="bg-white text-gray-900">Select department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" class="bg-white text-gray-900">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Year Level</label>
                                <input name="year_level" type="number" min="1" max="10" value="{{ old('year_level', $student->year_level) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Semester</label>
                                <select name="semester" class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" class="bg-white text-gray-900">Select semester</option>
                                    <option value="1" class="bg-white text-gray-900">1</option>
                                    <option value="2" class="bg-white text-gray-900">2</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Academic Year</label>
                                <input name="academic_year" type="text" value="{{ old('academic_year', now()->year . '-' . (now()->year + 1)) }}" class="mt-1 block w-full rounded-md border border-gray-300 bg-white text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="2025-2026" />
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Assign Curriculum</button>
                            </div>
                        </form>

                        @if($student->curriculums->count() > 0)
                            <div class="mb-6">
                                <h4 class="text-base font-semibold mb-3">Assigned Curriculum Groups</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Department</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Year Level</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Semester</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Academic Year</th>
                                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($student->curriculums as $assignment)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm">{{ $assignment->department->name ?? 'N/A' }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $assignment->year_level }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $assignment->semester }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ $assignment->academic_year }}</td>
                                                    <td class="px-4 py-2 text-sm">{{ ucfirst($assignment->status) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif

                    @if($student->academicHistories->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Course</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Faculty</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Academic Year</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Semester</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Grade</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-700 uppercase">Units</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($student->academicHistories as $history)
                                    <tr>
                                        <td class="px-4 py-2 text-sm">{{ $history->course->course_name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm">
                                            @php
                                                $teachers = $history->course->faculty->filter(function ($faculty) use ($history) {
                                                    return $faculty->pivot->academic_year === $history->academic_year && $faculty->pivot->semester === $history->semester;
                                                });
                                            @endphp

                                            @if($teachers->isNotEmpty())
                                                {{ $teachers->pluck('full_name')->join(', ') }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td class="px-4 py-2 text-sm">{{ $history->academic_year }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $history->semester }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $history->grade ?? 'N/A' }}</td>
                                        <td class="px-4 py-2 text-sm">{{ $history->units ?? 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No academic history found.</p>
                    @endif
                </div>
            </div>

            <!-- Activities -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg section-title">Activities</h3>
                        @if($isOwnProfile)
                            <p class="text-sm text-gray-600 mt-1">Submit activities for admin approval. Approved activities appear in reporting and search results.</p>
                        @endif

                        @if($isOwnProfile)
                            <form method="POST" action="{{ route('profile.student.activities.store') }}" enctype="multipart/form-data" class="submission-form grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                @csrf
                                <div class="md:col-span-2">
                                    <label class="submission-label">Activity Name</label>
                                    <input type="text" name="activity_name" value="{{ old('activity_name') }}" placeholder="Activity name" class="submission-input" required />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="submission-label">Description</label>
                                    <textarea name="description" rows="4" placeholder="Short description" class="submission-input">{{ old('description') }}</textarea>
                                </div>
                                <div>
                                    <label class="submission-label">Date</label>
                                    <input type="date" name="date" value="{{ old('date') }}" class="submission-input" required />
                                </div>
                                <div>
                                    <label class="submission-label">Status</label>
                                    <select name="status" class="submission-input" required>
                                        <option value="active" class="bg-white text-gray-900" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" class="bg-white text-gray-900" {{ old('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="inactive" class="bg-white text-gray-900" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="submission-label">Evidence Link</label>
                                    <input type="url" name="evidence_link" value="{{ old('evidence_link') }}" placeholder="Evidence link (optional)" class="submission-input" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="submission-label">Evidence File</label>
                                    <input type="file" name="evidence_file" class="submission-input file:mr-3 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit" class="cta-submit-btn">Submit Activity</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    @if($student->activities->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($student->activities as $activity)
                            <div class="border border-gray-300 rounded p-4 bg-white">
                                <div class="flex items-start justify-between gap-3">
                                    <h4 class="font-medium">{{ $activity->activity_name }}</h4>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $approvalBadgeClasses[$activity->approval_status ?? 'approved'] ?? $approvalBadgeClasses['approved'] }}">
                                        {{ ucfirst($activity->approval_status ?? 'approved') }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600">{{ $activity->description }}</p>
                                <p class="text-sm mt-2"><strong>Date:</strong> {{ $activity->date->format('M d, Y') }}</p>
                                <p class="text-sm"><strong>Status:</strong> {{ ucfirst($activity->status) }}</p>
                                @if($activity->evidence_link)
                                    <p class="text-sm"><a class="text-blue-600 underline" href="{{ $activity->evidence_link }}" target="_blank">View evidence link</a></p>
                                @endif
                                @if($activity->evidence_path)
                                    <p class="text-sm"><a class="text-blue-600 underline" href="{{ asset('storage/'.$activity->evidence_path) }}" target="_blank">View evidence file</a></p>
                                @endif
                                @if($activity->review_notes)
                                    <p class="text-sm mt-2"><strong>Review Notes:</strong> {{ $activity->review_notes }}</p>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No activities found.</p>
                    @endif
                </div>
            </div>

            <!-- Skills -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <div class="mb-4">
                        <h3 class="text-lg section-title">Skills</h3>
                        @if($isOwnProfile)
                            <p class="text-sm text-gray-600 mt-1">Submit skills for approval so they can be used in admin search and reporting.</p>
                        @endif

                        @if($isOwnProfile)
                            <form method="POST" action="{{ route('profile.student.skills.store') }}" enctype="multipart/form-data" class="submission-form grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                @csrf
                                <div class="md:col-span-2">
                                    <label class="submission-label">Skill Name</label>
                                    <input type="text" name="skill_name" value="{{ old('skill_name') }}" placeholder="Skill name" class="submission-input" required />
                                </div>
                                <div>
                                    <label class="submission-label">Proficiency</label>
                                    <select name="proficiency_level" class="submission-input" required>
                                    <option value="beginner" class="bg-white text-gray-900" {{ old('proficiency_level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="intermediate" class="bg-white text-gray-900" {{ old('proficiency_level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="advanced" class="bg-white text-gray-900" {{ old('proficiency_level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    <option value="expert" class="bg-white text-gray-900" {{ old('proficiency_level') === 'expert' ? 'selected' : '' }}>Expert</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="submission-label">Evidence Link</label>
                                    <input type="url" name="evidence_link" value="{{ old('evidence_link') }}" placeholder="Evidence link (optional)" class="submission-input" />
                                </div>
                                <div class="md:col-span-2">
                                    <label class="submission-label">Evidence File</label>
                                    <input type="file" name="evidence_file" class="submission-input file:mr-3 file:rounded file:border-0 file:bg-gray-100 file:px-3 file:py-2 file:text-sm" />
                                </div>
                                <div class="md:col-span-2">
                                    <button type="submit" class="cta-submit-btn">Submit Skill</button>
                                </div>
                            </form>
                        @endif
                    </div>

                    @if($student->skills->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($student->skills as $skill)
                            <div class="rounded-full border border-blue-200 bg-blue-100 px-4 py-2 text-sm text-blue-800 inline-block">
                                <span class="font-medium">{{ $skill->skill_name }}</span>
                                <span class="ml-2 text-xs text-blue-600">({{ ucfirst($skill->proficiency_level) }})</span>
                                <span class="ml-2 inline-block rounded-full px-2 py-0.5 text-xs font-semibold {{ $approvalBadgeClasses[$skill->approval_status ?? 'approved'] ?? $approvalBadgeClasses['approved'] }}">
                                    {{ ucfirst($skill->approval_status ?? 'approved') }}
                                </span>
                                @if($skill->review_notes)
                                    <div class="mt-1 text-xs text-blue-600">Note: {{ $skill->review_notes }}</div>
                                @endif
                                @if($skill->evidence_link)
                                    <div class="mt-1 text-xs"><a class="underline" href="{{ $skill->evidence_link }}" target="_blank">Evidence link</a></div>
                                @endif
                                @if($skill->evidence_path)
                                    <div class="mt-1 text-xs"><a class="underline" href="{{ asset('storage/'.$skill->evidence_path) }}" target="_blank">Evidence file</a></div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No skills found.</p>
                    @endif
                </div>
            </div>

            <!-- Affiliations -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white text-gray-900">
                    <h3 class="text-lg section-title mb-4">Affiliations</h3>
                    @if($student->affiliations->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($student->affiliations as $affiliation)
                            <div class="border border-gray-300 rounded p-4 bg-white">
                                <h4 class="font-medium">{{ $affiliation->affiliation_name }}</h4>
                                <p class="text-sm text-gray-600">{{ ucfirst($affiliation->affiliation_type) }}</p>
                                @if($affiliation->date_joined)
                                <p class="text-sm mt-2"><strong>Joined:</strong> {{ $affiliation->date_joined->format('M d, Y') }}</p>
                                @endif
                                <p class="text-sm"><strong>Status:</strong> {{ ucfirst($affiliation->status) }}</p>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No affiliations found.</p>
                    @endif
                </div>
            </div>

            <!-- Violations -->
            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white text-gray-900">
                    <h3 class="text-lg section-title mb-4">Violations</h3>
                    @if($canManageStudents)
                        <form method="POST" action="{{ route('students.violations.store', $student) }}" class="admin-form-card grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                            @csrf
                            <div class="md:col-span-5">
                                <label class="admin-field-label">Violation Type</label>
                                <input type="text" name="violation_type" placeholder="Violation type" class="admin-field-input" required>
                            </div>
                            <div class="md:col-span-3">
                                <label class="admin-field-label">Date</label>
                                <input type="date" name="date" class="admin-field-input" required>
                            </div>
                            <div class="md:col-span-2">
                                <label class="admin-field-label">Severity</label>
                                <select name="severity" class="admin-field-input" required>
                                <option value="minor">Minor</option>
                                <option value="moderate">Moderate</option>
                                <option value="serious">Serious</option>
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="admin-field-label">Status</label>
                                <select name="status" class="admin-field-input" required>
                                <option value="pending">Pending</option>
                                <option value="resolved">Resolved</option>
                                <option value="dismissed">Dismissed</option>
                                </select>
                            </div>
                            <div class="md:col-span-12">
                                <label class="admin-field-label">Description</label>
                                <textarea name="description" rows="3" placeholder="Description (optional)" class="admin-field-input"></textarea>
                            </div>
                            <div class="md:col-span-12">
                                <button class="rounded bg-red-600 text-white px-5 py-2.5 text-sm font-semibold">Add Violation</button>
                            </div>
                        </form>
                    @endif

                    @if($student->violations->count() > 0)
                        <div class="space-y-4">
                            @foreach($student->violations as $violation)
                            <div class="record-shell">
                                <h4 class="record-title text-red-700">{{ $violation->violation_type }}</h4>
                                <div class="record-meta">
                                    <div><strong>Date:</strong> {{ $violation->date->format('M d, Y') }}</div>
                                    <div><strong>Severity:</strong> {{ ucfirst($violation->severity) }}</div>
                                    <div><strong>Status:</strong> {{ ucfirst($violation->status) }}</div>
                                    @if($violation->description)
                                        <div><strong>Description:</strong> {{ $violation->description }}</div>
                                    @endif
                                </div>

                                @if($canManageStudents)
                                    <form method="POST" action="{{ route('students.violations.update', [$student, $violation]) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 mt-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="text" name="violation_type" value="{{ $violation->violation_type }}" class="admin-field-input md:col-span-5" required>
                                        <input type="date" name="date" value="{{ optional($violation->date)->format('Y-m-d') }}" class="admin-field-input md:col-span-3" required>
                                        <select name="severity" class="admin-field-input md:col-span-2" required>
                                            <option value="minor" @selected($violation->severity === 'minor')>Minor</option>
                                            <option value="moderate" @selected($violation->severity === 'moderate')>Moderate</option>
                                            <option value="serious" @selected($violation->severity === 'serious')>Serious</option>
                                        </select>
                                        <select name="status" class="admin-field-input md:col-span-2" required>
                                            <option value="pending" @selected($violation->status === 'pending')>Pending</option>
                                            <option value="resolved" @selected($violation->status === 'resolved')>Resolved</option>
                                            <option value="dismissed" @selected($violation->status === 'dismissed')>Dismissed</option>
                                        </select>
                                        <textarea name="description" rows="3" placeholder="Description (optional)" class="admin-field-input md:col-span-12">{{ $violation->description }}</textarea>
                                        <div class="md:col-span-12 flex items-center gap-2">
                                            <button class="rounded bg-gray-800 text-white px-4 py-2 text-sm">Update Violation</button>
                                        </div>
                                    </form>
                                    <form method="POST" action="{{ route('students.violations.destroy', [$student, $violation]) }}" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            onclick="return confirm('Delete this violation record?')"
                                            class="rounded bg-red-700 text-white px-4 py-2 text-sm"
                                        >
                                            Delete Violation
                                        </button>
                                    </form>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No violations found.</p>
                    @endif
                </div>
            </div>

            <div class="profile-panel bg-amber-50 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 bg-white text-gray-900">
                    <h3 class="text-lg section-title mb-4">Interventions & Case History</h3>
                    @if($isOwnProfile || auth()->user()->isFaculty() || $canManageStudents)
                        @if(auth()->user()->isFaculty() || $canManageStudents)
                            <form method="POST" action="{{ route('students.interventions.store', $student) }}" class="admin-form-card grid grid-cols-1 md:grid-cols-12 gap-4 mb-4">
                                @csrf
                                <div class="md:col-span-4">
                                    <label class="admin-field-label">Action Type</label>
                                    <select name="action_type" class="admin-field-input">
                                    <option value="called_guardian">Called guardian</option>
                                    <option value="advised_student">Advised student</option>
                                    <option value="referred_to_counselor">Referred to counselor</option>
                                    <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="md:col-span-3">
                                    <label class="admin-field-label">Due Date</label>
                                    <input type="date" name="due_date" class="admin-field-input">
                                </div>
                                <div class="md:col-span-5">
                                    <label class="admin-field-label">Notes</label>
                                    <input type="text" name="notes" placeholder="Intervention notes" class="admin-field-input">
                                </div>
                                <div class="md:col-span-12">
                                    <button class="rounded bg-blue-600 text-white px-5 py-2.5 text-sm font-semibold">Log Intervention</button>
                                </div>
                            </form>
                        @endif

                        <div class="space-y-3">
                            @forelse($student->interventions as $intervention)
                                <div class="record-shell">
                                    <div class="record-title">{{ str_replace('_', ' ', ucfirst($intervention->action_type)) }}</div>
                                    <div class="record-meta">Owner: {{ $intervention->assignee->name ?? $intervention->creator->name ?? 'N/A' }} | Due: {{ optional($intervention->due_date)->format('M d, Y') ?? 'N/A' }}</div>
                                    <div class="record-meta">{{ $intervention->notes }}</div>
                                    <div class="mt-2 text-sm">
                                        <span class="case-chip {{ $intervention->status === 'resolved' ? 'case-resolved' : ($intervention->status === 'in_progress' ? 'case-progress' : 'case-open') }}">
                                            {{ str_replace('_', ' ', strtoupper($intervention->status)) }}
                                        </span>
                                        @if($intervention->outcome)
                                            <span class="ml-2">Outcome: {{ $intervention->outcome }}</span>
                                        @endif
                                    </div>
                                    @if(auth()->user()->isFaculty() || $canManageStudents)
                                        <form method="POST" action="{{ route('students.interventions.update', [$student, $intervention]) }}" class="mt-4 grid grid-cols-1 md:grid-cols-12 gap-3">
                                            @csrf
                                            @method('PATCH')
                                            @if($canManageStudents)
                                                <select name="action_type" class="admin-field-input md:col-span-4">
                                                    <option value="called_guardian" @selected($intervention->action_type === 'called_guardian')>Called guardian</option>
                                                    <option value="advised_student" @selected($intervention->action_type === 'advised_student')>Advised student</option>
                                                    <option value="referred_to_counselor" @selected($intervention->action_type === 'referred_to_counselor')>Referred to counselor</option>
                                                    <option value="other" @selected($intervention->action_type === 'other')>Other</option>
                                                </select>
                                                <input type="date" name="due_date" value="{{ optional($intervention->due_date)->format('Y-m-d') }}" class="admin-field-input md:col-span-3">
                                                <input type="text" name="notes" value="{{ $intervention->notes }}" placeholder="Notes" class="admin-field-input md:col-span-5">
                                            @endif
                                            <select name="status" class="admin-field-input md:col-span-3">
                                                <option value="open" @selected($intervention->status === 'open')>Open</option>
                                                <option value="in_progress" @selected($intervention->status === 'in_progress')>In Progress</option>
                                                <option value="resolved" @selected($intervention->status === 'resolved')>Resolved</option>
                                            </select>
                                            <input type="text" name="outcome" value="{{ $intervention->outcome }}" placeholder="Outcome" class="admin-field-input md:col-span-7">
                                            <div class="md:col-span-2 flex items-center gap-2">
                                                <button class="rounded bg-gray-800 text-white px-4 py-2 text-sm">Update</button>
                                            </div>
                                        </form>
                                        @if(auth()->user()->isFaculty() || $canManageStudents)
                                            <form method="POST" action="{{ route('students.interventions.destroy', [$student, $intervention]) }}" class="mt-2">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    onclick="return confirm('Delete this intervention record?')"
                                                    class="rounded bg-red-700 text-white px-4 py-2 text-sm"
                                                >
                                                    Delete Intervention
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-600">No interventions yet.</p>
                            @endforelse
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
