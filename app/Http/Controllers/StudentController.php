<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;

class StudentController extends Controller
{
    /**
     * Display dashboard with students and courses
     */
    public function index()
    {

        $students = Student::with('course')->latest()->get();
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
        ]);

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
        ]);

        $student->update($validated);

        return redirect()->back()->with('success', 'Student updated successfully.');
    }

    /**
     * Delete a student
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Student deleted successfully.');
    }
}