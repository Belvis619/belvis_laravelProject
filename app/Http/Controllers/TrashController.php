<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Course;
use Illuminate\Support\Facades\Storage;

class TrashController extends Controller
{
    /**
     * Display all soft-deleted records
     */
    public function index()
    {
        $deletedStudents = Student::onlyTrashed()->with('course')->latest('deleted_at')->get();
        $deletedCourses = Course::onlyTrashed()->latest('deleted_at')->get();

        return view('trash', compact('deletedStudents', 'deletedCourses'));
    }

    /**
     * Restore a soft-deleted student
     */
    public function restoreStudent($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('trash.index')->with('success', 'Student restored successfully.');
    }

    /**
     * Permanently delete a student
     */
    public function forceDeleteStudent($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);

        // Delete photo if exists
        if ($student->photo && Storage::disk('public')->exists($student->photo)) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->forceDelete();

        return redirect()->route('trash.index')->with('success', 'Student permanently deleted.');
    }

    /**
     * Restore a soft-deleted course
     */
    public function restoreCourse($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->restore();

        return redirect()->route('trash.index')->with('success', 'Course restored successfully.');
    }

    /**
     * Permanently delete a course
     */
    public function forceDeleteCourse($id)
    {
        $course = Course::onlyTrashed()->findOrFail($id);
        $course->forceDelete();

        return redirect()->route('trash.index')->with('success', 'Course permanently deleted.');
    }
}
