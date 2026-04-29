<x-app-layout>
    <style>
        .form-container { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); max-width: 1100px; }
        .form-section-title { font-size: 1.125rem; font-weight: 700; color: #333; margin: 2rem 0 1rem 0; padding-bottom: 0.75rem; border-bottom: 2px solid #f0f0f0; }
        .form-section-title:first-child { margin-top: 0; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; font-size: 0.875rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; text-transform: capitalize; }
        .form-input, .form-select, .form-textarea { width: 100%; padding: 0.75rem 1rem; border: 1px solid #e0e0e0; border-radius: 8px; font-size: 0.875rem; font-family: inherit; transition: all 0.3s ease; }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: #5b5bee; box-shadow: 0 0 0 3px rgba(91, 91, 238, 0.1); background: #f8f9ff; }
        .form-textarea { resize: vertical; min-height: 100px; }
        .form-error { color: #ff6b6b; font-size: 0.75rem; margin-top: 0.25rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .form-grid.full { grid-template-columns: 1fr; }
        .assignment-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem; }
        .assignment-card { border: 1px solid #eadfda; border-radius: 12px; padding: 1rem; background: #fffaf7; }
        .assignment-card h4 { margin: 0 0 0.75rem; font-size: 0.95rem; font-weight: 700; color: #3b2b24; }
        .assignment-list { display: grid; gap: 0.75rem; max-height: 320px; overflow: auto; }
        .assignment-item { display: flex; align-items: flex-start; gap: 0.65rem; padding: 0.75rem; border-radius: 10px; background: white; border: 1px solid #f1e5df; }
        .assignment-item input[type="checkbox"] { margin-top: 0.15rem; }
        .assignment-meta { font-size: 0.8rem; color: #806454; margin-top: 0.2rem; }
        .student-select-block { display: none; margin-top: 0.85rem; padding-top: 0.85rem; border-top: 1px dashed #e5cbc0; }
        .student-select-block.is-visible { display: block; }
        .student-select { min-height: 140px; }
        .helper-copy { margin: 0 0 1rem; font-size: 0.86rem; color: #7a6256; }
        .form-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e0e0e0; }
        .btn-cancel { color: #999; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .btn-cancel:hover { color: #333; }
        .btn-submit { background: linear-gradient(135deg, #5b5bee 0%, #b85dd5 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 2rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        @media (max-width: 900px) {
            .form-grid, .assignment-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .form-actions { flex-direction: column-reverse; gap: 1rem; }
            .btn-submit { width: 100%; }
        }
    </style>

    <div style="background: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #333; margin: 0 0 0.25rem 0;">Edit Faculty Member</h1>
        <p style="font-size: 0.875rem; color: #999; margin: 0;">Update faculty details and keep the assigned courses, sections, and students in sync.</p>
    </div>

    <div class="form-container">
        <form method="POST" action="{{ route('faculty.update', $faculty) }}">
            @csrf
            @method('PATCH')

            <h3 class="form-section-title">Account Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input id="name" class="form-input @error('name') border-red-500 @enderror" type="text" name="name" value="{{ old('name', $faculty->user->name) }}" required />
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" class="form-input @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email', $faculty->email) }}" required />
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <h3 class="form-section-title">Faculty Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="faculty_id">Faculty ID</label>
                    <input id="faculty_id" class="form-input @error('faculty_id') border-red-500 @enderror" type="text" name="faculty_id" value="{{ old('faculty_id', $faculty->faculty_id) }}" required />
                    @error('faculty_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="department_id">Department</label>
                    <select id="department_id" name="department_id" class="form-select @error('department_id') border-red-500 @enderror" required>
                        <option value="">Select Department</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $faculty->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <input id="first_name" class="form-input @error('first_name') border-red-500 @enderror" type="text" name="first_name" value="{{ old('first_name', $faculty->first_name) }}" required />
                    @error('first_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input id="last_name" class="form-input @error('last_name') border-red-500 @enderror" type="text" name="last_name" value="{{ old('last_name', $faculty->last_name) }}" required />
                    @error('last_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input id="phone" class="form-input" type="text" name="phone" value="{{ old('phone', $faculty->phone) }}" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="office">Office</label>
                    <input id="office" class="form-input" type="text" name="office" value="{{ old('office', $faculty->office) }}" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="status">Status</label>
                    <select id="status" name="status" class="form-select @error('status') border-red-500 @enderror" required>
                        <option value="active" {{ old('status', $faculty->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $faculty->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="on_leave" {{ old('status', $faculty->status) == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                    </select>
                    @error('status')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group form-grid full">
                    <div>
                        <label class="form-label" for="specialization">Specialization</label>
                        <textarea id="specialization" name="specialization" class="form-textarea">{{ old('specialization', $faculty->specialization) }}</textarea>
                        @error('specialization')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <h3 class="form-section-title">Assignment Period</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="academic_year">Academic Year</label>
                    <input id="academic_year" class="form-input @error('academic_year') border-red-500 @enderror" type="text" name="academic_year" value="{{ old('academic_year', $academicYear) }}" required />
                    @error('academic_year')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="semester">Semester</label>
                    <select id="semester" name="semester" class="form-select @error('semester') border-red-500 @enderror" required>
                        <option value="1" {{ (string) old('semester', $semester) === '1' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2" {{ (string) old('semester', $semester) === '2' ? 'selected' : '' }}>2nd Semester</option>
                    </select>
                    @error('semester')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <h3 class="form-section-title">Teaching Assignments</h3>
            <p class="helper-copy">Keep the faculty-side course list, section list, and visible student roster aligned here. Selecting a section automatically keeps its course tied to the faculty member for the selected term.</p>
            <div class="assignment-grid">
                <div class="assignment-card">
                    <h4>Courses</h4>
                    <div class="assignment-list">
                        @foreach($courses as $course)
                            <label class="assignment-item">
                                <input type="checkbox" name="course_ids[]" value="{{ $course->id }}" {{ in_array($course->id, old('course_ids', $assignedCourses)) ? 'checked' : '' }}>
                                <span>
                                    <strong>{{ $course->course_code }} - {{ $course->course_name }}</strong>
                                    <div class="assignment-meta">{{ $course->department?->name ?? 'No department' }}</div>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @error('course_ids')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="assignment-card">
                    <h4>{{ $supportsSectionAssignments ? 'Sections and Students' : 'Course Students' }}</h4>
                    <div class="assignment-list">
                        @if($supportsSectionAssignments)
                            @foreach($sections as $section)
                                @php
                                    $selectedSectionIds = old('section_ids', $assignedSections);
                                @endphp
                                <div class="assignment-item section-assignment" data-section-id="{{ $section->id }}">
                                    <input
                                        type="checkbox"
                                        class="section-toggle"
                                        id="section_{{ $section->id }}"
                                        name="section_ids[]"
                                        value="{{ $section->id }}"
                                        {{ in_array($section->id, $selectedSectionIds) ? 'checked' : '' }}
                                    >
                                    <div style="flex: 1;">
                                        <label for="section_{{ $section->id }}"><strong>{{ $section->section_name }}</strong></label>
                                        <div class="assignment-meta">
                                            {{ $section->course?->course_code }} - {{ $section->course?->course_name }} |
                                            Year {{ $section->year_level }} |
                                            Semester {{ $section->semester }}
                                        </div>
                                        <div class="student-select-block {{ in_array($section->id, $selectedSectionIds) ? 'is-visible' : '' }}">
                                            <div class="assignment-meta">
                                                All students currently enrolled in this section are automatically assigned.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            @foreach($courses as $course)
                                @php
                                    $selectedStudents = old('course_students.' . $course->id, $courseStudentAssignments[$course->id] ?? []);
                                @endphp
                                <div class="assignment-item">
                                    <div style="flex: 1;">
                                        <strong>{{ $course->course_code }} - {{ $course->course_name }}</strong>
                                        <div class="assignment-meta">Assign students per course. Their own student section and year level are shown beside their names.</div>
                                        <div class="student-select-block is-visible">
                                            <label class="form-label" for="course_students_{{ $course->id }}">Students for {{ $course->course_code }}</label>
                                            <select
                                                id="course_students_{{ $course->id }}"
                                                class="form-select student-select"
                                                name="course_students[{{ $course->id }}][]"
                                                multiple
                                            >
                                                @foreach($students as $student)
                                                    <option value="{{ $student->id }}" {{ in_array($student->id, $selectedStudents) ? 'selected' : '' }}>
                                                        {{ $student->student_id }} - {{ $student->full_name }} | Year {{ $student->year_level }} | {{ $student->section ?: 'No Section' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    @error('section_ids')<div class="form-error">{{ $message }}</div>@enderror
                    @error('course_students')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('faculty.show', $faculty) }}" class="btn-cancel">&larr; Back to Profile</a>
                <button type="submit" class="btn-submit">Update Faculty</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.section-toggle').forEach(function (toggle) {
                const container = toggle.closest('.section-assignment');
                const studentBlock = container.querySelector('.student-select-block');

                const syncVisibility = function () {
                    studentBlock.classList.toggle('is-visible', toggle.checked);
                };

                toggle.addEventListener('change', syncVisibility);
                syncVisibility();
            });
        });
    </script>
</x-app-layout>
