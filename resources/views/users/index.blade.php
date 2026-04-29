<x-app-layout>
    <style>
        .page-title { font-size: 1.75rem; font-weight: 800; color: #23150d; margin-bottom: 0.25rem; }
        .page-subtitle { font-size: 0.9rem; color: #80553c; }
        .card-header { background: white; border-radius: 18px; padding: 1.5rem 2rem; margin-bottom: 2rem; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); display: flex; justify-content: space-between; align-items: center; gap: 1rem; border: 1px solid #f2d1b6; }
        .header-actions { display: flex; gap: 0.9rem; align-items: center; flex-wrap: wrap; justify-content: flex-end; }
        .table-card { background: white; border-radius: 18px; overflow: hidden; box-shadow: 0 16px 38px rgba(150, 73, 16, 0.08); border: 1px solid #f2d1b6; }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #fff7ef; border-bottom: 1px solid #f2d1b6; }
        th { padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #8b6349; letter-spacing: 0.08em; }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid #f3e3d6; font-size: 0.9rem; color: #33231a; vertical-align: top; }
        tbody tr:hover { background: #fffaf6; }
        tbody tr:last-child td { border-bottom: none; }
        .status-badge { display: inline-block; padding: 0.35rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; text-transform: capitalize; }
        .status-badge.admin { background: #e9d5ff; color: #6b21a8; }
        .status-badge.staff { background: #d0f7ff; color: #0c4a6e; }
        .status-badge.student { background: #d1fae5; color: #166534; }
        .status-badge.faculty { background: #fef3c7; color: #b45309; }
        .toolbar { display: flex; gap: 0.9rem; align-items: center; flex-wrap: wrap; }
        .toolbar-input, .toolbar-select { min-width: 180px; padding: 0.85rem 1rem; border-radius: 12px; border: 1px solid #ead8cb; font-size: 0.95rem; background: #fffdfa; }
        .toolbar-button, .create-button { border: none; border-radius: 999px; padding: 0.85rem 1.4rem; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .toolbar-button { background: #23150d; color: white; }
        .create-button { background: linear-gradient(135deg, #f36a10 0%, #bf4300 100%); color: white; box-shadow: 0 12px 24px rgba(191, 67, 0, 0.18); }
        .flash-message { background: #ecfdf3; border: 1px solid #9ad9b0; color: #146534; border-radius: 14px; padding: 0.95rem 1.1rem; margin-bottom: 1.25rem; font-weight: 700; }
        .error-message { background: #fff1f1; border: 1px solid #f1b6b6; color: #a32929; border-radius: 14px; padding: 0.95rem 1.1rem; margin-bottom: 1.25rem; font-weight: 700; }
        .secondary-text { color: #8b6349; font-size: 0.78rem; margin-top: 0.3rem; }
        .action-links { display: flex; gap: 0.85rem; flex-wrap: wrap; }
        .action-links a, .action-links button { color: #a24808; text-decoration: none; font-weight: 700; font-size: 0.82rem; transition: all 0.2s ease; background: none; border: none; padding: 0; cursor: pointer; font-family: inherit; }
        .action-links a:hover, .action-links button:hover { color: #33231a; }
        .action-links form { margin: 0; }
        .empty-state { padding: 3rem 2rem; text-align: center; }
        .empty-state-icon { margin-bottom: 1rem; display: inline-flex; color: #a24808; }
        .empty-state-text { color: #8b6349; margin-bottom: 1.5rem; }
        .pagination { margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; }
        .pagination a, .pagination span { padding: 0.5rem 0.75rem; border-radius: 8px; border: 1px solid #ead8cb; color: #a24808; text-decoration: none; font-size: 0.875rem; }
        .pagination a:hover { background: #fff7ef; }
        .pagination .active { background: #f36a10; color: white; border-color: #f36a10; }
        @media (max-width: 768px) {
            .card-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            .header-actions, .toolbar { width: 100%; justify-content: flex-start; }
            .toolbar-input, .toolbar-select { width: 100%; min-width: 0; }
            th, td { padding: 0.75rem 0.5rem; font-size: 0.75rem; }
        }
    </style>

    @if(session('success'))
        <div class="flash-message">{{ session('success') }}</div>
    @endif

    @if($errors->has('delete'))
        <div class="error-message">{{ $errors->first('delete') }}</div>
    @endif

    <div class="card-header">
        <div>
            <h1 class="page-title">User Management</h1>
            <p class="page-subtitle">Manage direct admin and staff access, and review student and faculty-linked accounts in one place.</p>
        </div>

        <div class="header-actions">
            <form method="GET" action="{{ route('users') }}" class="toolbar">
                <label for="search" class="sr-only">Search users</label>
                <input
                    id="search"
                    name="search"
                    type="text"
                    value="{{ old('search', $search ?? request('search')) }}"
                    placeholder="Search by name, email, or role"
                    class="toolbar-input"
                />

                <label for="role" class="sr-only">Filter by role</label>
                <select id="role" name="role" class="toolbar-select">
                    <option value="">All roles</option>
                    @foreach(array_merge($manageableRoles, ['student', 'faculty']) as $filterRole)
                        <option value="{{ $filterRole }}" {{ ($role ?? request('role')) === $filterRole ? 'selected' : '' }}>
                            {{ ucfirst($filterRole) }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="toolbar-button">Search</button>
            </form>

            <a href="{{ route('users.create') }}" class="create-button">+ Add User</a>
        </div>
    </div>

    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Profile ID</th>
                        <th>Management</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td><span class="status-badge {{ $user->role }}">{{ ucfirst($user->role) }}</span></td>
                            <td>
                                @if($user->student)
                                    {{ $user->student->student_id }}
                                @elseif($user->faculty)
                                    {{ $user->faculty->faculty_id }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td>
                                @if($user->student || $user->faculty)
                                    <div>Linked Profile</div>
                                    <div class="secondary-text">Manage from the student or faculty module.</div>
                                @else
                                    <div>Direct Access</div>
                                    <div class="secondary-text">Admin can edit role, email, and password here.</div>
                                @endif
                            </td>
                            <td>
                                <div class="action-links">
                                    @if($user->student)
                                        <a href="{{ route('students.show', $user->student) }}">View Profile</a>
                                    @elseif($user->faculty)
                                        <a href="{{ route('faculty.show', $user->faculty) }}">View Profile</a>
                                    @else
                                        <a href="{{ route('users.edit', $user) }}">Edit</a>

                                        <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem;">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg width="44" height="44" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M5 4.5H16.5C17.9 4.5 19 5.6 19 7V19.5H7.5C6.1 19.5 5 18.4 5 17V4.5Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <path d="M8 8H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M8 11H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M8 14H13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                    </div>
                                    <p class="empty-state-text">No users matched your filters. Try a different search, or create a new admin or staff account.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="pagination">
            {{ $users->links() }}
        </div>
    @endif
</x-app-layout>
