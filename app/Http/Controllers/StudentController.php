<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $latestLessons = \App\Models\LessonUserProgress::with(['lesson.module.course'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_accessed_at')
            ->take(4)
            ->get();

        $enrolledCourses = $request->user()->courses()
            ->orderByDesc('course_users.enrolled_at')
            ->get();

        return inertia('students/Index', [
            'latestLessons' => $latestLessons,
            'enrolledCourses' => $enrolledCourses,
        ]);
    }

    public function discover(Request $request)
    {
        $search = $request->input('search');

        $courses = Course::query()
            ->withExists(['users as is_enrolled' => function ($query) {
                $query->where('user_id', auth()->id());
            }])
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return inertia('students/Discover', [
            'courses' => $courses,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function enroll(Course $course)
    {
        $user = auth()->user();

        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            $user->courses()->attach($course->id, ['enrolled_at' => now()]);
        }

        return back();
    }
}
