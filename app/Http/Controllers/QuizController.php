<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Module;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;

class QuizController extends Controller
{
  /**
   * Store a newly created quiz in a module.
   */
  public function store(Request $request, Module $module)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'score' => 'required|integer|min:0|max:100',
      'time_limit' => 'required|integer|min:1',
    ]);

    $module->quizzes()->create([
      'title' => $validated['title'],
      'passing_score' => $validated['score'],
      'time_limit' => $validated['time_limit'],
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

    $maxOrder = $quiz->questions()->max('order') ?? -1;

    $question = $quiz->questions()->create([
      'question_text' => $validated['question_text'],
      'order' => $maxOrder + 1,
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
}
