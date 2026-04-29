<x-app-layout>
    <style>
        .page-title { font-size: 1.75rem; font-weight: 700; color: #333; margin-bottom: 0.25rem; }
        .page-subtitle { font-size: 0.875rem; color: #999; }
        .card-header { background: white; border-radius: 12px; padding: 1.5rem 2rem; margin-bottom: 2rem; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); display: flex; justify-content: space-between; align-items: center; }
        .btn-primary { background: linear-gradient(135deg, #5b5bee 0%, #b85dd5 100%); color: white; border: none; border-radius: 8px; padding: 0.75rem 1.5rem; font-weight: 600; font-size: 0.875rem; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); }
        .table-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05); }
        .table-wrapper { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: #f8f9fa; border-bottom: 2px solid #e9ecef; }
        th { padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; color: #666; letter-spacing: 0.5px; }
        td { padding: 1rem 1.5rem; border-bottom: 1px solid #e9ecef; font-size: 0.875rem; color: #333; }
        tbody tr:hover { background: #f8f9fa; }
        tbody tr:last-child td { border-bottom: none; }
        .status-badge { display: inline-block; padding: 0.375rem 0.75rem; border-radius: 6px; font-size: 0.75rem; font-weight: 600; text-transform: capitalize; }
        .status-badge.active { background: #d4edda; color: #155724; }
        .status-badge.inactive { background: #fff3cd; color: #856404; }
        .status-badge.graduated { background: #cfe2ff; color: #084298; }
        .action-links { display: flex; gap: 1rem; }
        .action-links a, .action-links button { color: #5b5bee; text-decoration: none; font-weight: 600; font-size: 0.875rem; border: none; background: none; cursor: pointer; transition: all 0.3s ease; }
        .action-links a:hover, .action-links button:hover { color: #b85dd5; }
        .action-links .delete-form button { color: #ff6b6b; }
        .action-links .delete-form button:hover { color: #ff5252; }
        .empty-state { padding: 3rem 2rem; text-align: center; }
        .empty-state-icon { margin-bottom: 1rem; display: inline-flex; color: #5b5bee; }
        .empty-state-text { color: #999; margin-bottom: 1.5rem; }
        .pagination { margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem; }
        .pagination a, .pagination span { padding: 0.5rem 0.75rem; border-radius: 6px; border: 1px solid #e9ecef; color: #5b5bee; text-decoration: none; font-size: 0.875rem; }
        .pagination a:hover { background: #f8f9fa; }
        .pagination .active { background: #5b5bee; color: white; border-color: #5b5bee; }
        @media (max-width: 768px) {
            .card-header { flex-direction: column; align-items: flex-start; gap: 1rem; }
            th, td { padding: 0.75rem 0.5rem; font-size: 0.75rem; }
        }
    </style>

    <div class="card-header">
        <div>
            <h1 class="page-title">Student Profiles</h1>
            <p class="page-subtitle">Manage and view all student information</p>
        </div>
        <a href="{{ route('students.create') }}" class="btn-primary">+ Add New Student</a>
    </div>

    <div class="table-card">
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_id }}</td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->year_level }}</td>
                        <td>{{ $student->section ?: 'N/A' }}</td>
                        <td>
                            <span class="status-badge {{ strtolower($student->status) }}">
                                {{ ucfirst($student->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-links">
                                <a href="{{ route('students.show', $student) }}">View</a>
                                <a href="{{ route('students.edit', $student) }}">Edit</a>
                                <form method="POST" action="{{ route('students.destroy', $student) }}" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this student?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit">Delete</button>
                                </form>
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
                                <p class="empty-state-text">No students found. <a href="{{ route('students.create') }}" style="color: #5b5bee; text-decoration: none; font-weight: 600;">Create one now</a></p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($students->hasPages())
    <div class="pagination">
        {{ $students->links() }}
    </div>
    @endif
</x-app-layout>
