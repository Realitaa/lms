<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Course;
use App\Models\Module;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuizController extends Controller
{
  /**
   * Store a newly created quiz for a module.
   */
  public function storeForModule(Request $request, Module $module)
  {
    return $this->createQuiz($request, $module);
  }

  /**
   * Store a newly created quiz for a lesson.
   */
  public function storeForLesson(Request $request, Lesson $lesson)
  {
    return $this->createQuiz($request, $lesson);
  }

  /**
   * Store a newly created quiz for a course.
   */
  public function storeForCourse(Request $request, Course $course)
  {
    return $this->createQuiz($request, $course);
  }

  /**
   * Shared helper to create a quiz on any quizable model.
   */
  private function createQuiz(Request $request, $quizable)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'score' => 'required|integer|min:0|max:100',
      'time_limit' => 'required|integer|min:1',
      'type' => 'required|in:pre,post',
    ]);

    $quizable->quizzes()->create([
      'title' => $validated['title'],
      'passing_score' => $validated['score'],
      'time_limit' => $validated['time_limit'],
      'type' => $validated['type'],
    ]);

    return back()->with('success', 'Kuis berhasil ditambahkan');
  }

  /**
   * Update the specified quiz.
   */
  public function update(Request $request, Quiz $quiz)
  {
    $validated = $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'passing_score' => 'sometimes|integer|min:0',
      'time_limit' => 'sometimes|nullable|integer|min:1',
      'type' => 'sometimes|in:pre,post',
    ]);

    $quiz->update($validated);

    return back()->with('success', 'Kuis berhasil diperbarui');
  }

  /**
   * Remove the specified quiz.
   */
  public function destroy(Quiz $quiz)
  {
    $quiz->delete();

    return back()->with('success', 'Kuis berhasil dihapus');
  }

  /**
   * Store a new question in a quiz.
   */
  public function storeQuestion(Request $request, Quiz $quiz)
  {
    $validated = $request->validate([
      'question_text' => 'required|array',
      'points' => 'sometimes|integer|min:1',
    ]);


    $question = $quiz->questions()->create([
      'question_text' => $validated['question_text'],
      'points' => $validated['points'] ?? 1,
    ]);

    return back()->with('success', 'Soal berhasil ditambahkan');
  }

  /**
   * Update a question.
   */
  public function updateQuestion(Request $request, Question $question)
  {
    $validated = $request->validate([
      'question_text' => 'sometimes|required|array',
      'points' => 'sometimes|integer|min:1',
    ]);

    $question->update($validated);

    return back()->with('success', 'Soal berhasil diperbarui');
  }

  /**
   * Delete a question.
   */
  public function destroyQuestion(Question $question)
  {
    $question->delete();

    return back()->with('success', 'Soal berhasil dihapus');
  }

  /**
   * Store a new option for a question.
   */
  public function storeOption(Request $request, Question $question)
  {
    $validated = $request->validate([
      'option_text' => 'required|array',
      'is_correct' => 'sometimes|boolean',
    ]);

    if ($validated['is_correct'] ?? false) {
      $question->options()->update(['is_correct' => false]);
    }

    $question->options()->create([
      'option_text' => $validated['option_text'],
      'is_correct' => $validated['is_correct'] ?? false,
    ]);

    return back()->with('success', 'Opsi berhasil ditambahkan');
  }

  /**
   * Update an option.
   */
  public function updateOption(Request $request, Option $option)
  {
    $validated = $request->validate([
      'option_text' => 'sometimes|required|array',
      'is_correct' => 'sometimes|boolean',
    ]);

    if ($validated['is_correct'] ?? false) {
      Option::where('question_id', $option->question_id)
        ->where('id', '!=', $option->id)
        ->update(['is_correct' => false]);
    }

    $option->update($validated);

    return back()->with('success', 'Opsi berhasil diperbarui');
  }

  /**
   * Delete an option.
   */
  public function destroyOption(Option $option)
  {
    $option->delete();

    return back()->with('success', 'Opsi berhasil dihapus');
  }

  public function index()
  {
    return Inertia::render('students/Quiz');
  }
}
