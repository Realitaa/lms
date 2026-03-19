<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        return inertia('students/Index');
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
