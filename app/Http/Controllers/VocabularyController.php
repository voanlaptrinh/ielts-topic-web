<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class VocabularyController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q'));
        $level = trim((string) $request->query('level'));
        $topic = trim((string) $request->query('topic'));

        $words = Vocabulary::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('word', 'like', "%{$search}%")
                        ->orWhere('meaning_vi', 'like', "%{$search}%")
                        ->orWhere('definition_en', 'like', "%{$search}%")
                        ->orWhere('topic', 'like', "%{$search}%");
                });
            })
            ->when($level, fn ($query) => $query->where('level', $level))
            ->when($topic, fn ($query) => $query->where('topic', $topic))
            ->orderBy('word')
            ->paginate(30)
            ->withQueryString();

        return view('vocabularies.index', [
            'words' => $words,
            'search' => $search,
            'selectedLevel' => $level,
            'selectedTopic' => $topic,
            'levels' => Vocabulary::select('level')->distinct()->orderBy('level')->pluck('level'),
            'topics' => Vocabulary::select('topic')->whereNotNull('topic')->distinct()->orderBy('topic')->pluck('topic'),
        ]);
    }

    public function show(string $word)
    {
        $vocabulary = Vocabulary::where('word', $word)->firstOrFail();

        return view('vocabularies.show', compact('vocabulary'));
    }

    public function flashcards()
    {
        $words = Vocabulary::orderBy('word')->get();

        return view('vocabularies.flashcards', compact('words'));
    }

    public function quiz()
    {
        $questions = $this->quizQuestions();

        return view('vocabularies.quiz', compact('questions'));
    }

    public function submitQuiz(Request $request)
    {
        $answers = $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'string'],
        ])['answers'];

        $words = Vocabulary::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $word = $words->get((int) $id);

            if (! $word) {
                continue;
            }

            $isCorrect = $answer === $word->meaning_vi;
            $score += $isCorrect ? 1 : 0;

            $results[] = [
                'word' => $word->word,
                'answer' => $answer,
                'correct' => $word->meaning_vi,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. \"{$word->word}\" có nghĩa là {$word->meaning_vi}."
                    : "Sai vì \"{$word->word}\" không có nghĩa là \"{$answer}\". Nghĩa đúng là: {$word->meaning_vi}. Ví dụ: {$word->example_en}",
            ];
        }

        if (auth()->check()) {
            TestAttempt::create([
                'user_id' => auth()->id(),
                'test_type' => 'Quiz từ vựng',
                'level' => 'Ôn nhanh',
                'score' => $score,
                'total' => count($results),
                'details' => $results,
            ]);
        }

        return view('vocabularies.quiz-result', [
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'wrongResults' => collect($results)->reject(fn ($result) => $result['is_correct'])->values(),
        ]);
    }

    private function quizQuestions()
    {
        $words = Vocabulary::query()
            ->where('meaning_vi', 'not like', 'Đang cập nhật%')
            ->inRandomOrder()
            ->take(5)
            ->get();

        $allMeanings = Vocabulary::query()
            ->where('meaning_vi', 'not like', 'Đang cập nhật%')
            ->pluck('meaning_vi')
            ->all();

        return $words->map(function (Vocabulary $word) use ($allMeanings) {
            $wrongAnswers = collect($allMeanings)
                ->reject(fn ($meaning) => $meaning === $word->meaning_vi)
                ->shuffle()
                ->take(3);

            return [
                'word' => $word,
                'options' => $wrongAnswers->push($word->meaning_vi)->shuffle()->values(),
            ];
        });
    }
}
