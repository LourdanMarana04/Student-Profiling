<?php

namespace App\Http\Controllers;

use App\Models\{CourseMaterial, Course, Section, Faculty};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseMaterialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Course $course)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('viewCourseMaterials', $course);

        $materials = $course->materials()
            ->where('faculty_id', $faculty->id)
            ->paginate(15);

        return view('faculty.materials.index', compact('course', 'materials'));
    }

    public function store(Request $request, Course $course)
    {
        $faculty = Faculty::where('user_id', auth()->id())->firstOrFail();

        $this->authorize('uploadMaterials', $course);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'section_id' => 'required|exists:sections,id',
            'file' => 'required|file|max:51200', // 50MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('course-materials', 'public');

        $material = CourseMaterial::create([
            'course_id' => $course->id,
            'section_id' => $validated['section_id'],
            'faculty_id' => $faculty->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'status' => 'published',
            'published_at' => now(),
        ]);

        return back()->with('success', 'Material uploaded successfully.');
    }

    public function destroy(CourseMaterial $material)
    {
        $this->authorize('delete', $material);

        if (Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return back()->with('success', 'Material deleted successfully.');
    }

    public function download(CourseMaterial $material)
    {
        $this->authorize('downloadMaterial', $material);

        return Storage::disk('public')->download($material->file_path);
    }
}
