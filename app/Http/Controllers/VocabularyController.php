<?php

namespace App\Http\Controllers;

use App\Models\TestAttempt;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class VocabularyController extends Controller
{
    public function index(Request $request)
    {
        return view('vocabularies.index', $this->searchData($request));
    }

    public function search(Request $request)
    {
        return view('vocabularies._results', $this->searchData($request));
    }

    public function show(string $word)
    {
        $vocabulary = Vocabulary::where('word', $word)->firstOrFail();

        return view('vocabularies.show', compact('vocabulary'));
    }

    public function flashcards()
    {
        $words = Vocabulary::orderBy('word')->paginate(24);

        if (request()->ajax()) {
            return view('vocabularies._flashcards', compact('words'));
        }

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
            ->inRandomOrder()
            ->take(5)
            ->get();

        $allMeanings = Vocabulary::query()
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

    private function searchData(Request $request): array
    {
        $search = $this->normalizeSearch((string) $request->query('q'));
        $activeTopic = trim((string) $request->query('topic'));

        $words = Vocabulary::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('word', 'like', "{$search}%")
                        ->orWhere('word', 'like', "%{$search}%")
                        ->orWhere('meaning_vi', 'like', "%{$search}%")
                        ->orWhere('definition_en', 'like', "%{$search}%")
                        ->orWhere('example_en', 'like', "%{$search}%")
                        ->orWhere('topic', 'like', "%{$search}%");
                });
            })
            ->when($search, fn ($query) => $query->orderByRaw(
                'CASE WHEN LOWER(word) = ? THEN 0 WHEN LOWER(word) LIKE ? THEN 1 ELSE 2 END',
                [mb_strtolower($search), mb_strtolower($search) . '%']
            ))
            ->orderBy('word')
            ->paginate(24)
            ->withQueryString();

        $featuredWord = $search ? $words->first() : null;
        $topicGroups = Vocabulary::query()
            ->selectRaw('topic, COUNT(*) as total')
            ->whereNotNull('topic')
            ->groupBy('topic')
            ->orderByDesc('total')
            ->orderBy('topic')
            ->get();

        if ($activeTopic === '' || ! $topicGroups->contains('topic', $activeTopic)) {
            $activeTopic = (string) ($topicGroups->first()?->topic ?? '');
        }

        $topicWords = $activeTopic === ''
            ? collect()
            : Vocabulary::where('topic', $activeTopic)
                ->orderBy('word')
                ->get();

        return [
            'words' => $words,
            'featuredWord' => $featuredWord,
            'search' => $search,
            'topicGroups' => $topicGroups,
            'activeTopic' => $activeTopic,
            'topicWords' => $topicWords,
        ];
    }

    private function normalizeSearch(string $value): string
    {
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/[^\pL\pN\s\']+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return trim($value);
    }
}
