<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\DiscussionThread;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        // Get all courses with modules and lessons for the filter dropdown
        $courses = Course::with('modules.lessons')->get();

        // Build the threads query
        $query = DiscussionThread::with(['user', 'lesson.module.course', 'replies.user'])
            ->withCount('replies');

        // Filter by lesson
        if ($request->filled('lesson_id')) {
            $query->where('lesson_id', $request->lesson_id);
        }

        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $threads = $query->latest()->get();

        return Inertia::render('discussion/Index', [
            'courses' => $courses,
            'threads' => $threads,
            'filters' => [
                'search' => $request->search,
                'lesson_id' => $request->lesson_id,
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $validated['user_id'] = $request->user()->id;

        DiscussionThread::create($validated);

        return redirect()->back();
    }

    public function storeReply(Request $request, DiscussionThread $thread)
    {
        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        $thread->replies()->create([
            'user_id' => $request->user()->id,
            'content' => $validated['content'],
        ]);

        return redirect()->back();
    }

    public function destroy(DiscussionThread $thread)
    {
        $thread->delete();

        return redirect()->back();
    }

    public function destroyReply(DiscussionReply $reply)
    {
        $reply->delete();

        return redirect()->back();
    }
}
