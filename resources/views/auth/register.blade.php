<x-guest-layout>
    <x-slot:title>Register</x-slot:title>

    <style>
        .register-shell {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .register-panel {
            background: #222b39;
            color: #fef6ee;
            border-radius: 22px;
            padding: 1.4rem;
            box-shadow: 0 16px 34px rgba(21, 26, 34, 0.18);
        }

        .register-panel-heading {
            font-size: 1.05rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: #fff5ea;
        }

        .register-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem 1.15rem;
        }

        .register-span-2 {
            grid-column: span 2;
        }

        .register-full {
            grid-column: 1 / -1;
        }

        .register-panel .form-label {
            color: #ffd9be;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .register-panel .modern-input {
            background: #2d3748;
            border-color: #3e4a5f;
            color: #fff7ef;
        }

        .register-panel .modern-input::placeholder {
            color: #bca99a;
        }

        .register-panel .modern-input:focus {
            border-color: #ff8f2f;
            box-shadow: 0 0 0 3px rgba(255, 143, 47, 0.18);
            background: #344053;
        }

        .register-textarea {
            min-height: 110px;
            resize: vertical;
        }

        .register-note {
            background: rgba(255, 247, 238, 0.95);
            border: 1px solid #f1c7a6;
            border-radius: 18px;
            padding: 1rem 1.15rem;
            color: #35251a;
        }

        .register-note h3 {
            font-size: 0.95rem;
            font-weight: 800;
            margin-bottom: 0.45rem;
            color: #8f3c05;
        }

        .register-note p {
            margin: 0;
            font-size: 0.9rem;
            line-height: 1.5;
            color: #6f4f39;
        }

        .register-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .register-login {
            font-size: 0.82rem;
            font-weight: 700;
            color: #6f4f39;
        }

        @media (max-width: 1100px) {
            .register-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .register-span-2 {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .register-grid {
                grid-template-columns: 1fr;
            }

            .register-span-2,
            .register-full {
                grid-column: auto;
            }

            .register-actions {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="register-shell">
            <div class="register-panel">
                <h3 class="register-panel-heading">Student Registration</h3>

                <div class="register-grid">
                    <div>
                        <label for="student_id" class="form-label">Student ID</label>
                        <input id="student_id" type="text" name="student_id" value="{{ old('student_id') }}" required autofocus placeholder="2024001" class="modern-input">
                        <x-input-error :messages="$errors->get('student_id')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required placeholder="student@email.com" class="modern-input">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="year_level" class="form-label">Year Level</label>
                        <select id="year_level" name="year_level" required class="modern-input">
                            <option value="">Select Year Level</option>
                            <option value="1" @selected(old('year_level') == '1')>1st Year</option>
                            <option value="2" @selected(old('year_level') == '2')>2nd Year</option>
                            <option value="3" @selected(old('year_level') == '3')>3rd Year</option>
                            <option value="4" @selected(old('year_level') == '4')>4th Year</option>
                        </select>
                        <x-input-error :messages="$errors->get('year_level')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="section" class="form-label">Section</label>
                        <input id="section" type="text" name="section" value="{{ old('section') }}" required placeholder="BSIT 1A" class="modern-input">
                        <x-input-error :messages="$errors->get('section')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="phone" class="form-label">Phone</label>
                        <input id="phone" type="text" name="phone" value="{{ old('phone') }}" placeholder="+63 912 345 6789" class="modern-input">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="first_name" class="form-label">First Name</label>
                        <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" required placeholder="John" class="modern-input">
                        <x-input-error :messages="$errors->get('first_name')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="last_name" class="form-label">Last Name</label>
                        <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}" required placeholder="Doe" class="modern-input">
                        <x-input-error :messages="$errors->get('last_name')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input id="date_of_birth" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="modern-input">
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div>
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="modern-input">
                            <option value="">Select Gender</option>
                            <option value="male" @selected(old('gender') == 'male')>Male</option>
                            <option value="female" @selected(old('gender') == 'female')>Female</option>
                            <option value="other" @selected(old('gender') == 'other')>Other</option>
                        </select>
                        <x-input-error :messages="$errors->get('gender')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div class="register-span-2">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" required placeholder="........" class="modern-input">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div class="register-span-2">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="........" class="modern-input">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-xs text-red-300" />
                    </div>

                    <div class="register-full">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" name="address" rows="4" placeholder="Street, Barangay, City, Province" class="modern-input register-textarea">{{ old('address') }}</textarea>
                        <x-input-error :messages="$errors->get('address')" class="mt-2 text-xs text-red-300" />
                    </div>
                </div>
            </div>

            <div class="register-actions">
                <button type="submit" class="modern-button" style="max-width: 420px;">
                    Create Student Account
                </button>

                <div class="register-login">
                    Already have an account?
                    <a href="{{ route('login') }}" class="switch-link ml-1">Login</a>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>
