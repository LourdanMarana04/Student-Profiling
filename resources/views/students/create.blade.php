<x-app-layout>
    <style>
        .form-container { background: white; border-radius: 12px; padding: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); max-width: 800px; }
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
        .form-actions { display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e0e0e0; }
        .btn-cancel { color: #999; text-decoration: none; font-weight: 600; transition: all 0.3s ease; }
        .btn-cancel:hover { color: #333; }
        .btn-submit { background: linear-gradient(135deg, #5b5bee 0%, #b85dd5 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 2rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .form-actions { flex-direction: column-reverse; gap: 1rem; }
            .btn-submit { width: 100%; }
        }
    </style>

    <div style="background: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
        <h1 style="font-size: 1.75rem; font-weight: 700; color: #333; margin: 0 0 0.25rem 0;">Add New Student</h1>
        <p style="font-size: 0.875rem; color: #999; margin: 0;">Fill in the student information below</p>
    </div>

    <div class="form-container">
        <form method="POST" action="{{ route('students.store') }}">
            @csrf
            <h3 class="form-section-title">Account Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input id="name" class="form-input @error('name') border-red-500 @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus />
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input id="email" class="form-input @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email') }}" required />
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" class="form-input @error('password') border-red-500 @enderror" type="password" name="password" required />
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required />
                </div>
            </div>

            <h3 class="form-section-title">Student Information</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="student_id">Student ID</label>
                    <input id="student_id" class="form-input @error('student_id') border-red-500 @enderror" type="text" name="student_id" value="{{ old('student_id') }}" required />
                    @error('student_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="year_level">Year Level</label>
                    <select id="year_level" name="year_level" class="form-select @error('year_level') border-red-500 @enderror" required>
                        <option value="">Select Year Level</option>
                        <option value="1" {{ old('year_level') == '1' ? 'selected' : '' }}>1st Year</option>
                        <option value="2" {{ old('year_level') == '2' ? 'selected' : '' }}>2nd Year</option>
                        <option value="3" {{ old('year_level') == '3' ? 'selected' : '' }}>3rd Year</option>
                        <option value="4" {{ old('year_level') == '4' ? 'selected' : '' }}>4th Year</option>
                    </select>
                    @error('year_level')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="section">Section</label>
                    <input id="section" class="form-input @error('section') border-red-500 @enderror" type="text" name="section" value="{{ old('section') }}" required />
                    @error('section')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <input id="first_name" class="form-input @error('first_name') border-red-500 @enderror" type="text" name="first_name" value="{{ old('first_name') }}" required />
                    @error('first_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input id="last_name" class="form-input @error('last_name') border-red-500 @enderror" type="text" name="last_name" value="{{ old('last_name') }}" required />
                    @error('last_name')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label" for="date_of_birth">Date of Birth</label>
                    <input id="date_of_birth" class="form-input" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" />
                </div>
                <div class="form-group">
                    <label class="form-label" for="gender">Gender</label>
                    <select id="gender" name="gender" class="form-select">
                        <option value="">Select Gender</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="form-group form-grid full">
                    <div>
                        <label class="form-label" for="address">Address</label>
                        <textarea id="address" name="address" class="form-textarea">{{ old('address') }}</textarea>
                        @error('address')<div class="form-error">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone</label>
                    <input id="phone" class="form-input" type="text" name="phone" value="{{ old('phone') }}" />
                </div>
            </div>

            <div class="form-actions">
                <a href="{{ route('students.index') }}" class="btn-cancel">&larr; Back to Students</a>
                <button type="submit" class="btn-submit">Create Student</button>
            </div>
        </form>
    </div>
</x-app-layout>
