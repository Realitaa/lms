<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Http\Request;

class LessonController extends Controller
{
  /**
   * Store a newly created lesson in a module.
   */
  public function store(Request $request, Module $module)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
    ]);

    $maxOrder = $module->lessons()->max('order') ?? -1;

    $module->lessons()->create([
      'title' => $validated['title'],
      'content' => [],
      'order' => $maxOrder + 1,
    ]);

    return back()->with('success', 'Materi berhasil ditambahkan');
  }

  /**
   * Display the specified lesson.
   */
  public function show(Lesson $lesson)
  {
    return $lesson;
  }

  /**
   * Update the specified lesson (title and/or content).
   */
  public function update(Request $request, Lesson $lesson)
  {
    $validated = $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'content' => 'sometimes|nullable|array',
    ]);

    $lesson->update($validated);

    return back()->with('success', 'Materi berhasil diperbarui');
  }

  /**
   * Remove the specified lesson.
   */
  public function destroy(Lesson $lesson)
  {
    $lesson->delete();

    return back()->with('success', 'Materi berhasil dihapus');
  }

  /**
   * Reorder lessons (supports cross-module moves).
   */
  public function reorder(Request $request, Course $course)
  {
    $validated = $request->validate([
      'lessons' => 'required|array',
      'lessons.*.id' => 'required|integer|exists:lessons,id',
      'lessons.*.order' => 'required|integer|min:0',
      'lessons.*.module_id' => 'required|integer|exists:modules,id',
    ]);

    foreach ($validated['lessons'] as $item) {
      Lesson::where('id', $item['id'])->update([
        'order' => $item['order'],
        'module_id' => $item['module_id'],
      ]);
    }

    return back()->with('success', 'Urutan materi berhasil diperbarui');
  }
}
