<?php

namespace App\Http\Controllers;

use App\Models\{GradeComponent, GradeEntry, Section, Faculty};
use Illuminate\Http\Request;

class GradeBookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Section $section)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('manageGradeBook', $section);

        $components = $section->course->gradeComponents()
            ->where('section_id', $section->id)
            ->orderBy('sort_order')
            ->get();

        $students = $section->students()->get();

        $grades = [];
        foreach ($students as $student) {
            $grades[$student->id] = [];
            foreach ($components as $component) {
                $entry = GradeEntry::firstOrCreate(
                    [
                        'grade_component_id' => $component->id,
                        'student_id' => $student->id,
                    ]
                );
                $grades[$student->id][$component->id] = $entry;
            }
        }

        return view('faculty.gradebook.index', compact('section', 'components', 'students', 'grades'));
    }

    public function updateGrades(Request $request, Section $section)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('manageGradeBook', $section);

        $validated = $request->validate([
            'grades.*.*' => 'nullable|numeric|min:0',
            'notes.*.*' => 'nullable|string',
        ]);

        foreach ($validated['grades'] ?? [] as $entryId => $score) {
            $entry = GradeEntry::findOrFail($entryId);
            $this->authorize('updateGrade', $entry);
            $entry->update([
                'score' => $score,
                'notes' => $validated['notes'][$entryId] ?? null,
            ]);
        }

        return back()->with('success', 'Grades updated successfully.');
    }

    public function computeFinalGrades(Section $section)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('manageGradeBook', $section);

        $students = $section->students()->get();
        $components = $section->course->gradeComponents()
            ->where('section_id', $section->id)
            ->get();

        $finalGrades = [];

        foreach ($students as $student) {
            $totalScore = 0;
            $totalWeight = 0;

            foreach ($components as $component) {
                $entry = GradeEntry::where([
                    'grade_component_id' => $component->id,
                    'student_id' => $student->id,
                ])->first();

                if ($entry && $entry->score !== null) {
                    $percentage = ($entry->score / $component->total_points) * 100;
                    $weightedScore = ($percentage * $component->weight) / 100;
                    $totalScore += $weightedScore;
                    $totalWeight += $component->weight;
                }
            }

            $finalGrades[$student->id] = $totalWeight > 0
                ? round($totalScore * 100 / $totalWeight, 2)
                : 0;
        }

        return view('faculty.gradebook.final-grades', compact('section', 'finalGrades', 'students'));
    }
}
