<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Quiz;
use App\Models\QuizAttempts;
use App\Models\LessonUserProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LearningController extends Controller
{
    /**
     * Student learning index - redirect to first course or show course list.
     */
    public function index()
    {
        return inertia('students/Learning');
    }

    /**
     * Show a specific lesson within a course.
     */
    public function show(Request $request, Course $course, Lesson $lesson)
    {
        $user = $request->user();

        // Verify enrollment
        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.discover')
                ->with('error', 'Anda belum terdaftar di kursus ini.');
        }

        // Load course with modules, lessons, and their quizzes
        $course->load([
            'modules.lessons.quizzes',
            'modules.quizzes',
            'quizzes',
        ]);

        // Convert TipTap JSON content to HTML
        $lessonHtml = '';
        if ($lesson->content) {
            $content = is_string($lesson->content) ? json_decode($lesson->content, true) : $lesson->content;
            if ($content) {
                $lessonHtml = $this->tiptapJsonToHtml($content);
            }
        }

        // Load lesson quizzes
        $lesson->load('quizzes.questions');

        // Track lesson progress
        $progress = LessonUserProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'lesson_id' => $lesson->id,
            ],
            [
                'last_accessed_at' => now(),
            ]
        );

        // Get all lesson progress for this user in this course
        $lessonIds = $course->modules->flatMap(fn($m) => $m->lessons->pluck('id'));
        $allProgress = LessonUserProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessonIds)
            ->get()
            ->keyBy('lesson_id');

        // Get all quiz attempts for this user in this course
        $allQuizIds = collect();
        // Course-level quizzes
        $allQuizIds = $allQuizIds->merge($course->quizzes->pluck('id'));
        // Module-level quizzes
        foreach ($course->modules as $module) {
            $allQuizIds = $allQuizIds->merge($module->quizzes->pluck('id'));
            // Lesson-level quizzes
            foreach ($module->lessons as $l) {
                $allQuizIds = $allQuizIds->merge($l->quizzes->pluck('id'));
            }
        }

        $quizAttempts = QuizAttempts::where('user_id', $user->id)
            ->whereIn('quiz_id', $allQuizIds)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('quiz_id');

        // Determine progression status - build a map of which lessons/modules can be accessed
        $accessMap = $this->buildAccessMap($course, $quizAttempts, $allProgress);

        // Get discussion threads for this lesson
        $threads = $lesson->threads()
            ->with(['user', 'replies.user'])
            ->withCount('replies')
            ->latest()
            ->get();

        // Get all courses with modules and lessons for discussion filter
        $discussionCourses = Course::whereHas('users', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with('modules.lessons')->get();

        return Inertia::render('students/Learning', [
            'course' => $course,
            'currentLesson' => $lesson,
            'lessonHtml' => $lessonHtml,
            'modules' => $course->modules,
            'progress' => $allProgress,
            'quizAttempts' => $quizAttempts,
            'accessMap' => $accessMap,
            'threads' => $threads,
            'discussionCourses' => $discussionCourses,
        ]);
    }

    /**
     * Show a quiz page for a student.
     */
    public function showQuiz(Request $request, Course $course, Quiz $quiz)
    {
        $user = $request->user();

        // Verify enrollment
        if (!$user->courses()->where('course_id', $course->id)->exists()) {
            return redirect()->route('student.discover');
        }

        // Load quiz with questions and options (shuffle options, exclude is_correct)
        $quiz->load(['questions.options']);

        // Check for an existing unfinished attempt
        $existingAttempt = QuizAttempts::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->latest()
            ->first();

        // Get past attempts
        $pastAttempts = QuizAttempts::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNotNull('finished_at')
            ->orderByDesc('created_at')
            ->get();

        // Prepare questions without revealing correct answers
        $questions = $quiz->questions->map(function ($question) {
            return [
                'id' => $question->id,
                'question_text' => $question->question_text,
                'points' => $question->points,
                'options' => $question->options->shuffle()->map(function ($option) {
                    return [
                        'id' => $option->id,
                        'option_text' => $option->option_text,
                    ];
                }),
            ];
        });

        return Inertia::render('students/Quiz', [
            'course' => $course,
            'quiz' => $quiz->only(['id', 'title', 'passing_score', 'time_limit', 'type']),
            'questions' => $questions,
            'existingAttempt' => $existingAttempt,
            'pastAttempts' => $pastAttempts,
        ]);
    }

    /**
     * Start a new quiz attempt.
     */
    public function startQuiz(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        // Check for existing unfinished attempt
        $existingAttempt = QuizAttempts::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->first();

        if ($existingAttempt) {
            return back()->with('attempt', $existingAttempt);
        }

        // Get the latest attempt number
        $lastAttempt = QuizAttempts::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->max('attempt_number');

        $attempt = QuizAttempts::create([
            'user_id' => $user->id,
            'quiz_id' => $quiz->id,
            'attempt_number' => ($lastAttempt ?? 0) + 1,
            'started_at' => now(),
        ]);

        return back()->with('success', 'Kuis dimulai');
    }

    /**
     * Submit quiz answers and calculate score.
     */
    public function submitQuiz(Request $request, Quiz $quiz)
    {
        $user = $request->user();

        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*.question_id' => 'required|integer|exists:questions,id',
            'answers.*.option_id' => 'nullable|integer|exists:options,id',
        ]);

        // Find the active attempt
        $attempt = QuizAttempts::where('user_id', $user->id)
            ->where('quiz_id', $quiz->id)
            ->whereNull('finished_at')
            ->latest()
            ->firstOrFail();

        // Save individual answers
        $totalPoints = 0;
        $earnedPoints = 0;

        $quiz->load('questions.options');

        foreach ($quiz->questions as $question) {
            $totalPoints += $question->points;

            $answerData = collect($validated['answers'])
                ->firstWhere('question_id', $question->id);

            $selectedOptionId = $answerData['option_id'] ?? null;

            // Save the answer
            $attempt->answers()->create([
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
            ]);

            // Check if correct
            if ($selectedOptionId) {
                $correctOption = $question->options->firstWhere('is_correct', true);
                if ($correctOption && $correctOption->id == $selectedOptionId) {
                    $earnedPoints += $question->points;
                }
            }
        }

        // Calculate percentage score
        $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
        $isPassed = $score >= $quiz->passing_score;

        // Update attempt
        $attempt->update([
            'score' => $score,
            'is_passed' => $isPassed,
            'finished_at' => now(),
        ]);

        // If passed, mark lesson progress as completed (if quiz is on a lesson)
        if ($isPassed) {
            $quizable = $quiz->quizable;
            if ($quizable instanceof \App\Models\Lesson) {
                LessonUserProgress::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'lesson_id' => $quizable->id,
                    ],
                    [
                        'is_completed' => true,
                        'completed_at' => now(),
                    ]
                );
            }
        }

        return back()->with([
            'quizResult' => [
                'score' => $score,
                'isPassed' => $isPassed,
                'passingScore' => $quiz->passing_score,
                'earnedPoints' => $earnedPoints,
                'totalPoints' => $totalPoints,
            ],
        ]);
    }

    /**
     * Build access map to determine which lessons can be accessed.
     * A lesson is accessible if all quizzes in the previous lesson (and module post-quiz) have been passed.
     */
    private function buildAccessMap(Course $course, $quizAttempts, $allProgress): array
    {
        $accessMap = [];
        $previousLessonPassed = true; // First lesson is always accessible

        foreach ($course->modules as $moduleIndex => $module) {
            // Check module pre-quiz if exists
            $modulePreQuiz = $module->quizzes->firstWhere('type', 'pre');
            if ($modulePreQuiz && $moduleIndex > 0) {
                $passed = $this->isQuizPassed($modulePreQuiz->id, $quizAttempts);
                if (!$passed) {
                    // All lessons in this module are locked
                    foreach ($module->lessons as $lesson) {
                        $accessMap['lesson_' . $lesson->id] = false;
                    }
                    $accessMap['module_' . $module->id] = false;
                    $previousLessonPassed = false;
                    continue;
                }
            }

            $accessMap['module_' . $module->id] = true;

            foreach ($module->lessons as $lessonIndex => $lesson) {
                if ($moduleIndex === 0 && $lessonIndex === 0) {
                    // First lesson of first module is always accessible
                    $accessMap['lesson_' . $lesson->id] = true;
                } else {
                    $accessMap['lesson_' . $lesson->id] = $previousLessonPassed;
                }

                // Check if this lesson's quizzes are passed (for next lesson access)
                $lessonQuizzes = $lesson->quizzes;
                if ($lessonQuizzes->count() > 0) {
                    $allPassed = $lessonQuizzes->every(function ($quiz) use ($quizAttempts) {
                        return $this->isQuizPassed($quiz->id, $quizAttempts);
                    });
                    $previousLessonPassed = $allPassed;
                } else {
                    // No quizzes = lesson is considered passed for progression
                    $previousLessonPassed = true;
                }
            }

            // Check module post-quiz for next module access
            $modulePostQuiz = $module->quizzes->firstWhere('type', 'post');
            if ($modulePostQuiz) {
                $previousLessonPassed = $this->isQuizPassed($modulePostQuiz->id, $quizAttempts);
            }
        }

        return $accessMap;
    }

    /**
     * Check if a quiz has been passed by looking at attempts.
     */
    private function isQuizPassed(int $quizId, $quizAttempts): bool
    {
        $attempts = $quizAttempts->get($quizId, collect());
        return $attempts->contains('is_passed', true);
    }

    /**
     * Convert TipTap JSON to HTML using the same extensions as the frontend.
     */
    private function tiptapJsonToHtml(array $json): string
    {
        // Simple server-side TipTap JSON to HTML conversion
        if (!isset($json['type']) || $json['type'] !== 'doc' || !isset($json['content'])) {
            return '';
        }

        return $this->renderNodes($json['content']);
    }

    private function renderNodes(array $nodes): string
    {
        $html = '';
        foreach ($nodes as $node) {
            $html .= $this->renderNode($node);
        }
        return $html;
    }

    private function renderNode(array $node): string
    {
        $type = $node['type'] ?? '';
        $content = isset($node['content']) ? $this->renderNodes($node['content']) : '';
        $attrs = $node['attrs'] ?? [];

        switch ($type) {
            case 'paragraph':
                return "<p>{$content}</p>";
            case 'heading':
                $level = $attrs['level'] ?? 1;
                return "<h{$level}>{$content}</h{$level}>";
            case 'text':
                $text = htmlspecialchars($node['text'] ?? '', ENT_QUOTES, 'UTF-8');
                // Apply marks
                if (isset($node['marks'])) {
                    foreach ($node['marks'] as $mark) {
                        switch ($mark['type']) {
                            case 'bold':
                                $text = "<strong>{$text}</strong>";
                                break;
                            case 'italic':
                                $text = "<em>{$text}</em>";
                                break;
                            case 'underline':
                                $text = "<u>{$text}</u>";
                                break;
                            case 'strike':
                                $text = "<s>{$text}</s>";
                                break;
                            case 'code':
                                $text = "<code>{$text}</code>";
                                break;
                            case 'link':
                                $href = htmlspecialchars($mark['attrs']['href'] ?? '#', ENT_QUOTES, 'UTF-8');
                                $target = $mark['attrs']['target'] ?? '_blank';
                                $text = "<a href=\"{$href}\" target=\"{$target}\">{$text}</a>";
                                break;
                            case 'textStyle':
                                $styles = '';
                                if (isset($mark['attrs']['color'])) {
                                    $styles .= 'color: ' . htmlspecialchars($mark['attrs']['color']) . ';';
                                }
                                if ($styles) {
                                    $text = "<span style=\"{$styles}\">{$text}</span>";
                                }
                                break;
                            case 'superscript':
                                $text = "<sup>{$text}</sup>";
                                break;
                            case 'subscript':
                                $text = "<sub>{$text}</sub>";
                                break;
                        }
                    }
                }
                return $text;
            case 'bulletList':
                return "<ul>{$content}</ul>";
            case 'orderedList':
                $start = $attrs['start'] ?? 1;
                return "<ol start=\"{$start}\">{$content}</ol>";
            case 'listItem':
                return "<li>{$content}</li>";
            case 'blockquote':
                return "<blockquote>{$content}</blockquote>";
            case 'codeBlock':
                $language = htmlspecialchars($attrs['language'] ?? '', ENT_QUOTES, 'UTF-8');
                return "<pre><code class=\"language-{$language}\">{$content}</code></pre>";
            case 'hardBreak':
                return '<br>';
            case 'horizontalRule':
                return '<hr>';
            case 'image':
                $src = htmlspecialchars($attrs['src'] ?? '', ENT_QUOTES, 'UTF-8');
                $alt = htmlspecialchars($attrs['alt'] ?? '', ENT_QUOTES, 'UTF-8');
                $title = htmlspecialchars($attrs['title'] ?? '', ENT_QUOTES, 'UTF-8');
                return "<img src=\"{$src}\" alt=\"{$alt}\" title=\"{$title}\">";
            case 'table':
                return "<table>{$content}</table>";
            case 'tableRow':
                return "<tr>{$content}</tr>";
            case 'tableHeader':
                return "<th>{$content}</th>";
            case 'tableCell':
                return "<td>{$content}</td>";
            case 'mathBlock':
                $math = $attrs['latex'] ?? ($node['text'] ?? '');
                return '<div class="math-display">$$' . $math . '$$</div>';
            case 'mathInline':
                $math = $attrs['latex'] ?? ($node['text'] ?? '');
                return '<span class="math-inline">$' . $math . '$</span>';
            default:
                return $content;
        }
    }
}
