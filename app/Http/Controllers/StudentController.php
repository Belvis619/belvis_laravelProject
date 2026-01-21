<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    /**
     * Display dashboard with students and courses
     */
    public function index(Request $request)
    {
        $query = Student::with('course')->latest();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by course
        if ($request->filled('course_filter')) {
            $query->where('course_id', $request->course_filter);
        }

        $students = $query->get();
        $courses = Course::all();
        $activeCourses = Course::count();

        return view('dashboard', compact('students', 'courses', 'activeCourses'));
    }

    /**
     * Store a new student
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('students/photos', $photoName, 'public');
            $validated['photo'] = $photoPath;
        }

        Student::create($validated);

        return redirect()->back()->with('success', 'Student added successfully.');
    }

    /**
     * Update an existing student
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }

            $photo = $request->file('photo');
            $photoName = time() . '_' . Str::random(10) . '.' . $photo->getClientOriginalExtension();
            $photoPath = $photo->storeAs('students/photos', $photoName, 'public');
            $validated['photo'] = $photoPath;
        }

        $student->update($validated);

        return redirect()->back()->with('success', 'Student updated successfully.');
    }

    /**
     * Delete a student (soft delete)
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted successfully.');
    }

    /**
     * Export students to PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Student::with('course')->latest();

        // Apply same search and filter as index
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('course_filter')) {
            $query->where('course_id', $request->course_filter);
        }

        $students = $query->get();
        $timestamp = now()->format('Y-m-d_H-i-s');

        $pdf = Pdf::loadView('pdf.students', compact('students'));
        return $pdf->download("students_export_{$timestamp}.pdf");
    }
}