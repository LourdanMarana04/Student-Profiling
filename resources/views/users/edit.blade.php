<x-app-layout>
    <style>
        .form-shell { background: white; border-radius: 18px; padding: 2rem; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); max-width: 860px; border: 1px solid #f2d1b6; }
        .page-hero { background: linear-gradient(135deg, #fff7ef 0%, #fff1e3 100%); border: 1px solid #f2d1b6; border-radius: 18px; padding: 1.6rem 1.9rem; margin-bottom: 1.75rem; box-shadow: 0 12px 28px rgba(150, 73, 16, 0.06); }
        .page-title { font-size: 1.8rem; font-weight: 800; color: #23150d; margin: 0 0 0.35rem; }
        .page-copy { margin: 0; color: #80553c; font-size: 0.95rem; }
        .section-title { font-size: 1.08rem; font-weight: 800; color: #23150d; margin: 0 0 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid #f2d1b6; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1.25rem 1.5rem; }
        .form-group { margin-bottom: 1.35rem; }
        .form-label { display: block; margin-bottom: 0.5rem; font-weight: 700; color: #33231a; font-size: 0.9rem; }
        .form-input, .form-select { width: 100%; border: 1px solid #e6d3c6; border-radius: 12px; padding: 0.85rem 1rem; font-size: 0.95rem; transition: all 0.2s ease; background: #fffdfa; }
        .form-input:focus, .form-select:focus { outline: none; border-color: #f36a10; box-shadow: 0 0 0 3px rgba(243, 106, 16, 0.12); background: white; }
        .form-error { margin-top: 0.35rem; color: #d14343; font-size: 0.78rem; }
        .helper-note { margin-top: -0.25rem; margin-bottom: 1rem; color: #8b6349; font-size: 0.86rem; }
        .actions { display: flex; justify-content: space-between; align-items: center; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #f2d1b6; }
        .back-link { color: #8b6349; text-decoration: none; font-weight: 700; }
        .back-link:hover { color: #33231a; }
        .submit-button { border: none; border-radius: 999px; padding: 0.85rem 1.5rem; background: linear-gradient(135deg, #f36a10 0%, #bf4300 100%); color: white; font-weight: 800; cursor: pointer; box-shadow: 0 12px 24px rgba(191, 67, 0, 0.2); }
        .submit-button:hover { transform: translateY(-1px); }
        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .actions { flex-direction: column-reverse; align-items: stretch; }
            .submit-button { width: 100%; }
        }
    </style>

    <div class="page-hero">
        <h1 class="page-title">Edit User Account</h1>
        <p class="page-copy">Update direct system access for {{ $user->name }}. Leave the password blank if you do not want to change it.</p>
    </div>

    <div class="form-shell">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PATCH')

            <h2 class="section-title">Account Details</h2>
            <p class="helper-note">This screen manages direct admin and staff accounts.</p>

            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input id="name" class="form-input @error('name') border-red-500 @enderror" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                    @error('name')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input id="email" class="form-input @error('email') border-red-500 @enderror" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="role">Role</label>
                    <select id="role" name="role" class="form-select @error('role') border-red-500 @enderror" required>
                        @foreach($manageableRoles as $role)
                            <option value="{{ $role }}" {{ old('role', $user->role) === $role ? 'selected' : '' }}>{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">New Password</label>
                    <input id="password" class="form-input @error('password') border-red-500 @enderror" type="password" name="password" />
                    @error('password')<div class="form-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm New Password</label>
                    <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" />
                </div>
            </div>

            <div class="actions">
                <a href="{{ route('users') }}" class="back-link">&larr; Back to Users</a>
                <button type="submit" class="submit-button">Save Changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
