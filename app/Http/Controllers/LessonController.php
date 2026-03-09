<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
   * Handles moving temporary images to permanent storage.
   */
  public function update(Request $request, Lesson $lesson)
  {
    $validated = $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'content' => 'sometimes|nullable|array',
      'temp_images' => 'sometimes|array',
      'temp_images.*' => 'string',
    ]);

    // If content includes temporary images, move them to permanent storage
    if (isset($validated['content']) && !empty($validated['temp_images'])) {
      $urlMap = $this->moveTempImages($validated['temp_images']);

      // Replace temp URLs with permanent URLs in the content JSON
      $contentJson = json_encode($validated['content']);
      foreach ($urlMap as $oldUrl => $newUrl) {
        $contentJson = str_replace($oldUrl, $newUrl, $contentJson);
      }
      $validated['content'] = json_decode($contentJson, true);
    }

    unset($validated['temp_images']);
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

  /**
   * Move temporary images to permanent storage.
   *
   * @param array $tempPaths Array of temp file paths (e.g., 'uploads/tmp/uuid_filename.jpg')
   * @return array Map of old URL => new URL
   */
  private function moveTempImages(array $tempPaths): array
  {
    $disk = Storage::disk('public');
    $urlMap = [];

    foreach ($tempPaths as $tempPath) {
      if (!Str::startsWith($tempPath, 'uploads/tmp/')) {
        continue;
      }

      if (!$disk->exists($tempPath)) {
        continue;
      }

      $filename = basename($tempPath);
      $permanentPath = 'uploads/images/' . $filename;

      $disk->move($tempPath, $permanentPath);

      $urlMap[$disk->url($tempPath)] = $disk->url($permanentPath);
    }

    return $urlMap;
  }
}
