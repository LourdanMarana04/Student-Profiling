<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentActivity;
use App\Models\StudentSkill;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class QueryController extends Controller
{
    /**
     * Display the query/filtering interface
     */
    public function index()
    {
        $this->ensureCanManageStudents();

        return view('queries.index');
    }

    /**
     * Search students by skill
     */
    public function searchBySkill(Request $request)
    {
        $this->ensureCanManageStudents();

        $skill = $request->get('skill');
        $format = $request->get('format', 'table'); // table, list, cards

        $query = Student::with(['user', 'skills', 'activities', 'affiliations']);

        if ($skill) {
            $query->whereHas('skills', function ($q) use ($skill) {
                $q->where('approval_status', StudentSkill::APPROVAL_APPROVED)
                    ->where('skill_name', 'LIKE', '%' . $skill . '%');
            });
        }

        $students = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'students' => $students,
                'format' => $format
            ]);
        }

        return view('queries.results', compact('students', 'skill', 'format'));
    }

    /**
     * Search students by activity
     */
    public function searchByActivity(Request $request)
    {
        $this->ensureCanManageStudents();

        $activity = $request->get('activity');
        $format = $request->get('format', 'table');

        $query = Student::with(['user', 'skills', 'activities', 'affiliations']);

        if ($activity) {
            $query->whereHas('activities', function ($q) use ($activity) {
                $q->where('approval_status', StudentActivity::APPROVAL_APPROVED)
                    ->where('activity_name', 'LIKE', '%' . $activity . '%');
            });
        }

        $students = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'students' => $students,
                'format' => $format
            ]);
        }

        return view('queries.results', compact('students', 'activity', 'format'));
    }

    /**
     * Advanced multi-condition filtering
     */
    public function advancedSearch(Request $request)
    {
        $this->ensureCanManageStudents();

        $query = Student::with(['user', 'skills', 'activities', 'affiliations']);

        // Skill filter
        if ($request->filled('skill')) {
            $query->whereHas('skills', function ($q) use ($request) {
                $q->where('approval_status', StudentSkill::APPROVAL_APPROVED)
                    ->where('skill_name', 'LIKE', '%' . $request->skill . '%');
            });
        }

        // Activity filter
        if ($request->filled('activity')) {
            $query->whereHas('activities', function ($q) use ($request) {
                $q->where('approval_status', StudentActivity::APPROVAL_APPROVED)
                    ->where('activity_name', 'LIKE', '%' . $request->activity . '%');
            });
        }

        // Year level filter
        if ($request->filled('year_level')) {
            $query->where('year_level', $request->year_level);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $students = $query->paginate(10);
        $format = $request->get('format', 'table');

        return view('queries.results', compact('students', 'format'));
    }

    /**
     * Get all available skills for autocomplete
     */
    public function getSkills()
    {
        $this->ensureCanManageStudents();

        $skills = StudentSkill::select('skill_name')
            ->where('approval_status', StudentSkill::APPROVAL_APPROVED)
            ->distinct()
            ->pluck('skill_name')
            ->toArray();

        return response()->json($skills);
    }

    /**
     * Get all available activities for autocomplete
     */
    public function getActivities()
    {
        $this->ensureCanManageStudents();

        $activities = StudentActivity::select('activity_name')
            ->where('approval_status', StudentActivity::APPROVAL_APPROVED)
            ->distinct()
            ->pluck('activity_name')
            ->toArray();

        return response()->json($activities);
    }

    private function ensureCanManageStudents(): void
    {
        abort_unless(auth()->user()?->canManageStudents(), Response::HTTP_FORBIDDEN);
    }
}
