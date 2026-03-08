<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Course;
use App\Models\Module;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Course $course)
    {
        $course->load([
            'modules' => function ($query) {
                $query->orderBy('order')->with([
                    'lessons' => function ($q) {
                        $q->orderBy('order');
                    }
                ]);
            }
        ]);

        return Inertia::render('modules/Index', [
            'course' => $course,
        ]);
    }

    /**
     * Store a newly created module in a course.
     */
    public function store(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $maxOrder = $course->modules()->max('order') ?? -1;

        $course->modules()->create([
            'title' => $validated['title'],
            'order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Modul berhasil ditambahkan');
    }

    /**
     * Update the specified module.
     */
    public function update(Request $request, Course $course, Module $module)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $module->update($validated);

        return back()->with('success', 'Modul berhasil diperbarui');
    }

    /**
     * Remove the specified module.
     */
    public function destroy(Course $course, Module $module)
    {
        $module->delete();

        return back()->with('success', 'Modul berhasil dihapus');
    }

    /**
     * Reorder modules.
     */
    public function reorder(Request $request, Course $course)
    {
        $validated = $request->validate([
            'modules' => 'required|array',
            'modules.*.id' => 'required|integer|exists:modules,id',
            'modules.*.order' => 'required|integer|min:0',
        ]);

        foreach ($validated['modules'] as $item) {
            Module::where('id', $item['id'])->update([
                'order' => $item['order'],
            ]);
        }

        return back()->with('success', 'Urutan modul berhasil diperbarui');
    }
}
