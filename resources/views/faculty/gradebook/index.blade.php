@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Grade Book - {{ $section->course->course_name }} ({{ $section->section_name }})</h1>
        <div>
            <a href="{{ route('faculty.gradebook.final', $section) }}" class="btn btn-success">Compute Final Grades</a>
            <button class="btn btn-primary" onclick="saveGrades()">Save Changes</button>
        </div>
    </div>

    <form id="gradesForm" method="POST" action="{{ route('faculty.gradebook.update', $section) }}">
        @csrf
        @method('PATCH')

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        @foreach($components as $component)
                            <th>{{ $component->name }} ({{ $component->weight }}%)</th>
                        @endforeach
                        <th>Final Grade</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $student)
                        <tr>
                            <td>{{ $student->student_id }}</td>
                            <td>{{ $student->full_name }}</td>
                            @foreach($components as $component)
                                <td>
                                    <input type="number"
                                           class="form-control form-control-sm"
                                           name="grades[{{ $grades[$student->id][$component->id]->id }}]"
                                           value="{{ $grades[$student->id][$component->id]->score }}"
                                           min="0"
                                           max="{{ $component->total_points }}"
                                           step="0.01">
                                    <textarea class="form-control form-control-sm mt-1"
                                              name="notes[{{ $grades[$student->id][$component->id]->id }}]"
                                              placeholder="Notes"
                                              rows="1">{{ $grades[$student->id][$component->id]->notes }}</textarea>
                                </td>
                            @endforeach
                            <td>
                                <span id="final-grade-{{ $student->id }}">--</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>
</div>

<script>
function saveGrades() {
    const form = document.getElementById('gradesForm');
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Grades saved successfully!');
            location.reload();
        } else {
            alert('Error saving grades');
        }
    });
}

function calculateFinalGrades() {
    // This would calculate final grades based on component weights
    // Implementation would depend on the grading formula
}
</script>
@endsection