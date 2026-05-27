<?php

namespace App\Http\Controllers;

use App\Models\DictionaryEntry;
use App\Models\TestAttempt;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class PracticeTestController extends Controller
{
    private const QUESTIONS_PER_TEST = 100;

    public function index()
    {
        return view('tests.index', [
            'levels' => $this->levels(),
        ]);
    }

    public function level(string $level)
    {
        $config = $this->levelConfig($level);
        $skillPractices = $this->skillPracticeConfigs();

        return view('tests.level', compact('level', 'config', 'skillPractices'));
    }

    public function vocabulary(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);

        $words = Vocabulary::query()
            ->when($config['vocabulary_levels'], fn ($query) => $query->whereIn('level', $config['vocabulary_levels']))
            ->inRandomOrder()
            ->take($config['question_count'])
            ->get();

        if ($words->count() < $config['question_count']) {
            $fallbackWords = Vocabulary::query()
                ->whereNotIn('id', $words->pluck('id'))
                ->inRandomOrder()
                ->take($config['question_count'] - $words->count())
                ->get();

            $words = $words->concat($fallbackWords);
        }

        $allMeanings = Vocabulary::all()
            ->map(fn (Vocabulary $word) => $this->vocabularyAnswer($word))
            ->unique()
            ->values()
            ->all();

        $questions = $words->map(function (Vocabulary $word) use ($allMeanings) {
            $correctAnswer = $this->vocabularyAnswer($word);
            $options = collect($allMeanings)
                ->reject(fn ($meaning) => $meaning === $correctAnswer)
                ->shuffle()
                ->take(3)
                ->push($correctAnswer)
                ->shuffle()
                ->values();

            return [
                'id' => $word->id,
                'word' => $word->word,
                'prompt' => "Chọn nghĩa đúng của từ \"{$word->word}\".",
                'options' => $options,
            ];
        });

        return view('tests.vocabulary', compact('questions', 'level', 'config'));
    }

    public function submitVocabulary(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $words = Vocabulary::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $word = $words->get((int) $id);

            if (! $word) {
                continue;
            }

            $correctAnswer = $this->vocabularyAnswer($word);
            $isCorrect = $answer === $correctAnswer;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $word->word,
                'answer' => $answer,
                'correct' => $correctAnswer,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. \"{$word->word}\" có nghĩa là {$correctAnswer}."
                    : "Sai vì \"{$word->word}\" không có nghĩa là \"{$answer}\". Nghĩa đúng là: {$correctAnswer}. Ví dụ: {$word->example_en}",
            ];
        }

        $this->recordAttempt('Từ vựng', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test từ vựng - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function grammar(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);

        $entries = DictionaryEntry::whereIn('part_of_speech', ['noun', 'verb', 'adjective', 'adverb'])
            ->whereNotNull('definition')
            ->whereRaw('LENGTH(word) <= ?', [$config['max_word_length']])
            ->whereRaw('LENGTH(definition) <= ?', [$config['max_definition_length']])
            ->inRandomOrder()
            ->take($config['question_count'])
            ->get();

        return view('tests.grammar', [
            'questions' => $entries,
            'options' => ['noun', 'verb', 'adjective', 'adverb'],
            'level' => $level,
            'config' => $config,
        ]);
    }

    public function submitGrammar(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $entries = DictionaryEntry::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $entry = $entries->get((int) $id);

            if (! $entry) {
                continue;
            }

            $isCorrect = $answer === $entry->part_of_speech;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $entry->word,
                'answer' => $this->posLabel($answer),
                'correct' => $this->posLabel($entry->part_of_speech),
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. Từ này đang được dùng như {$this->posLabel($entry->part_of_speech)}."
                    : "Sai vì nghĩa này của \"{$entry->word}\" là {$this->posLabel($entry->part_of_speech)}. {$this->grammarReason($entry->part_of_speech)} Định nghĩa: {$entry->definition}",
            ];
        }

        $this->recordAttempt('Loại từ', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test loại từ - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function sentenceRole(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);
        $questions = collect($this->expandedSentenceRoleQuestions())
            ->whereIn('level', $config['sentence_levels'])
            ->shuffle()
            ->take($config['question_count']);

        return view('tests.sentence-role', [
            'questions' => $questions,
            'options' => ['chủ ngữ', 'vị ngữ', 'tân ngữ', 'bổ ngữ', 'trạng ngữ'],
            'level' => $level,
            'config' => $config,
        ]);
    }

    public function submitSentenceRole(Request $request, string $level = 'intermediate')
    {
        $payload = $this->validatedSentenceRolePayload($request);
        $answers = $payload['answers'];
        $correctAnswers = $payload['correct'];
        $explanations = $payload['explanations'];
        $targets = $payload['targets'];
        $results = [];
        $score = 0;

        foreach ($correctAnswers as $index => $correct) {
            $answer = $answers[$index] ?? '';
            $isCorrect = $answer === $correct;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $targets[$index] ?? 'Câu hỏi',
                'answer' => $answer ?: 'Chưa chọn',
                'correct' => $correct,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect ? 'Đúng. ' . $explanations[$index] : 'Sai. ' . $explanations[$index],
            ];
        }

        $this->recordAttempt('Thành phần câu', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test thành phần câu - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function definition(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);
        $entries = $this->dictionaryEntriesForLevel($config)
            ->inRandomOrder()
            ->take($config['question_count'])
            ->get();
        $optionPool = $entries->pluck('word')->unique()->values();

        $questions = $entries->map(function (DictionaryEntry $entry) use ($optionPool) {
            return [
                'id' => $entry->id,
                'prompt' => $entry->definition,
                'detail' => $entry->definition_vi,
                'options' => $this->choiceOptions($entry->word, $optionPool),
            ];
        });

        return view('tests.multiple-choice', [
            'title' => 'Bài test chọn từ theo định nghĩa - ' . $config['name'],
            'subtitle' => $config['band'] . '. Đọc định nghĩa và chọn từ đúng nhất.',
            'questions' => $questions,
            'action' => route('tests.definition.submit', $level),
        ]);
    }

    public function submitDefinition(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $entries = DictionaryEntry::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $entry = $entries->get((int) $id);

            if (! $entry) {
                continue;
            }

            $isCorrect = $answer === $entry->word;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $entry->definition,
                'answer' => $answer,
                'correct' => $entry->word,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. Định nghĩa này nói về từ \"{$entry->word}\"."
                    : "Sai. Từ đúng là \"{$entry->word}\". Định nghĩa: {$entry->definition}",
            ];
        }

        $this->recordAttempt('Định nghĩa', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test chọn từ theo định nghĩa - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function spelling(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);
        $words = $this->vocabularyWordsForLevel($config);

        $questions = $words->map(fn (Vocabulary $word) => [
            'id' => $word->id,
            'prompt' => 'Chọn cách viết đúng của từ này.',
            'detail' => $this->vocabularyAnswer($word),
            'options' => $this->spellingOptions($word->word),
        ]);

        return view('tests.multiple-choice', [
            'title' => 'Bài test chính tả - ' . $config['name'],
            'subtitle' => $config['band'] . '. Chọn từ được viết đúng.',
            'questions' => $questions,
            'action' => route('tests.spelling.submit', $level),
        ]);
    }

    public function submitSpelling(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $words = Vocabulary::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $word = $words->get((int) $id);

            if (! $word) {
                continue;
            }

            $isCorrect = $answer === $word->word;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $this->vocabularyAnswer($word),
                'answer' => $answer,
                'correct' => $word->word,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. Cách viết chuẩn là \"{$word->word}\"."
                    : "Sai. Cách viết đúng là \"{$word->word}\".",
            ];
        }

        $this->recordAttempt('Chính tả', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test chính tả - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function exampleCompletion(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);
        $words = $this->vocabularyWordsForLevel($config);
        $optionPool = $words->pluck('word')->unique()->values();

        $questions = $words->map(function (Vocabulary $word) use ($optionPool) {
            return [
                'id' => $word->id,
                'prompt' => $this->blankExample($word),
                'detail' => $this->vocabularyAnswer($word),
                'options' => $this->choiceOptions($word->word, $optionPool),
            ];
        });

        return view('tests.multiple-choice', [
            'title' => 'Bài test điền từ vào câu - ' . $config['name'],
            'subtitle' => $config['band'] . '. Đọc ngữ cảnh và chọn từ còn thiếu.',
            'questions' => $questions,
            'action' => route('tests.example-completion.submit', $level),
        ]);
    }

    public function submitExampleCompletion(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $words = Vocabulary::whereIn('id', array_keys($answers))->get()->keyBy('id');
        $results = [];
        $score = 0;

        foreach ($answers as $id => $answer) {
            $word = $words->get((int) $id);

            if (! $word) {
                continue;
            }

            $isCorrect = $answer === $word->word;
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $this->blankExample($word),
                'answer' => $answer,
                'correct' => $word->word,
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect
                    ? "Đúng. Câu đầy đủ: {$word->example_en}"
                    : "Sai. Từ cần điền là \"{$word->word}\". Câu đầy đủ: {$word->example_en}",
            ];
        }

        $this->recordAttempt('Điền từ vào câu', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài test điền từ vào câu - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function ieltsFormat(string $level = 'intermediate')
    {
        $config = $this->levelConfig($level);

        return view('tests.multiple-choice', [
            'title' => 'Bài tổng hợp theo dạng IELTS - ' . $config['name'],
            'subtitle' => $config['band'] . '. Câu hỏi gốc được tổng hợp theo dạng bài chính thức: đọc hiểu, hoàn thành câu, chọn tiêu đề, ý chính và suy luận.',
            'questions' => collect($this->ieltsFormatQuestions($level))->map(fn ($question, $index) => [
                'id' => $index,
                'prompt' => $question['prompt'],
                'detail' => $question['detail'],
                'options' => $question['options'],
            ]),
            'action' => route('tests.ielts-format.submit', $level),
        ]);
    }

    public function submitIeltsFormat(Request $request, string $level = 'intermediate')
    {
        $answers = $this->validatedAnswers($request);
        $questions = collect($this->ieltsFormatQuestions($level));
        $results = [];
        $score = 0;

        foreach ($answers as $index => $answer) {
            $question = $questions->get((int) $index);

            if (! $question) {
                continue;
            }

            $isCorrect = $answer === $question['correct'];
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $question['prompt'],
                'answer' => $answer,
                'correct' => $question['correct'],
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect ? 'Đúng. ' . $question['explanation'] : 'Sai. ' . $question['explanation'],
            ];
        }

        $this->recordAttempt('Tổng hợp dạng IELTS', $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả bài tổng hợp theo dạng IELTS - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    public function skillPractice(string $level, string $skill)
    {
        $config = $this->levelConfig($level);
        $practice = $this->skillPracticeConfig($skill);

        return view('tests.multiple-choice', [
            'title' => $practice['title'] . ' - ' . $config['name'],
            'subtitle' => $config['band'] . '. ' . $practice['subtitle'],
            'questions' => collect($this->skillPracticeQuestions($level, $skill))->map(fn ($question, $index) => [
                'id' => $index,
                'prompt' => $question['prompt'],
                'detail' => $question['detail'],
                'options' => $question['options'],
            ]),
            'action' => route('tests.skill.submit', [$level, $skill]),
        ]);
    }

    public function submitSkillPractice(Request $request, string $level, string $skill)
    {
        $practice = $this->skillPracticeConfig($skill);
        $answers = $this->validatedAnswers($request);
        $questions = collect($this->skillPracticeQuestions($level, $skill));
        $results = [];
        $score = 0;

        foreach ($answers as $index => $answer) {
            $question = $questions->get((int) $index);

            if (! $question) {
                continue;
            }

            $isCorrect = $answer === $question['correct'];
            $score += $isCorrect ? 1 : 0;
            $results[] = [
                'word' => $question['prompt'],
                'answer' => $answer,
                'correct' => $question['correct'],
                'is_correct' => $isCorrect,
                'explanation' => $isCorrect ? 'Đúng. ' . $question['explanation'] : 'Sai. ' . $question['explanation'],
            ];
        }

        $this->recordAttempt($practice['history'], $level, $score, count($results), $results);

        return view('tests.result', [
            'title' => 'Kết quả ' . mb_strtolower($practice['title']) . ' - ' . $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => count($results),
            'results' => $results,
            'level' => $level,
        ]);
    }

    private function sentenceRoleQuestions(): array
    {
        return [
            [
                'level' => 'foundation',
                'sentence' => 'Students learn English.',
                'target' => 'Students',
                'correct' => 'chủ ngữ',
                'explanation' => '"Students" đứng trước động từ "learn" và là người thực hiện hành động, nên là chủ ngữ.',
            ],
            [
                'level' => 'foundation',
                'sentence' => 'She reads books.',
                'target' => 'reads',
                'correct' => 'vị ngữ',
                'explanation' => '"reads" là động từ chính của câu, nên thuộc phần vị ngữ.',
            ],
            [
                'level' => 'elementary',
                'sentence' => 'Many people use public transport.',
                'target' => 'public transport',
                'correct' => 'tân ngữ',
                'explanation' => 'Cụm này đứng sau động từ "use" và nhận hành động, nên là tân ngữ.',
            ],
            [
                'level' => 'elementary',
                'sentence' => 'The lesson is useful.',
                'target' => 'useful',
                'correct' => 'bổ ngữ',
                'explanation' => '"useful" đứng sau "is" để mô tả "lesson", nên là bổ ngữ.',
            ],
            [
                'level' => 'pre-intermediate',
                'sentence' => 'Sustainable transport reduces air pollution.',
                'target' => 'Sustainable transport',
                'correct' => 'chủ ngữ',
                'explanation' => 'Cụm này đứng trước động từ "reduces" và thực hiện hành động, nên là chủ ngữ.',
            ],
            [
                'level' => 'pre-intermediate',
                'sentence' => 'Students should allocate enough time for revision.',
                'target' => 'allocate',
                'correct' => 'vị ngữ',
                'explanation' => '"allocate" là động từ chính sau chủ ngữ "Students", nên thuộc phần vị ngữ.',
            ],
            [
                'level' => 'intermediate',
                'sentence' => 'Online courses are beneficial for busy learners.',
                'target' => 'beneficial',
                'correct' => 'bổ ngữ',
                'explanation' => '"beneficial" đứng sau "are" để mô tả chủ ngữ, nên là bổ ngữ tính từ.',
            ],
            [
                'level' => 'intermediate',
                'sentence' => 'The city expanded rapidly.',
                'target' => 'rapidly',
                'correct' => 'trạng ngữ',
                'explanation' => '"rapidly" bổ nghĩa cho động từ "expanded", nên là trạng ngữ.',
            ],
            [
                'level' => 'upper-intermediate',
                'sentence' => 'Heavy traffic causes serious consequences.',
                'target' => 'serious consequences',
                'correct' => 'tân ngữ',
                'explanation' => 'Cụm này đứng sau động từ "causes" và nhận tác động của hành động, nên là tân ngữ.',
            ],
            [
                'level' => 'advanced',
                'sentence' => 'What many learners underestimate is the importance of consistent revision.',
                'target' => 'What many learners underestimate',
                'correct' => 'chủ ngữ',
                'explanation' => 'Mệnh đề danh từ này đứng trước "is" và đóng vai trò chủ ngữ của cả câu.',
            ],
        ];
    }

    private function posLabel(string $pos): string
    {
        return [
            'noun' => 'danh từ',
            'verb' => 'động từ',
            'adjective' => 'tính từ',
            'adverb' => 'trạng từ',
        ][$pos] ?? $pos;
    }

    private function vocabularyAnswer(Vocabulary $word): string
    {
        $meaning = trim((string) $word->meaning_vi);

        if ($meaning !== '' && ! $this->isLegacyVocabularyPlaceholder($meaning)) {
            return $meaning;
        }

        return $word->definition_en ?: "Academic meaning of {$word->word}";
    }

    private function isLegacyVocabularyPlaceholder(string $value): bool
    {
        return str_starts_with($value, implode(' ', ['Đang', 'cập', 'nhật']));
    }

    private function expandedSentenceRoleQuestions(): array
    {
        $questions = $this->sentenceRoleQuestions();
        $subjects = [
            'foundation' => ['Students', 'Teachers', 'Children', 'Friends', 'Learners'],
            'elementary' => ['Daily practice', 'Clear examples', 'Short lessons', 'Group work', 'Simple grammar'],
            'pre-intermediate' => ['Regular revision', 'Good planning', 'Focused reading', 'Listening practice', 'A clear outline'],
            'intermediate' => ['Effective feedback', 'Online learning', 'Student motivation', 'Independent learning', 'Academic reading'],
            'upper-intermediate' => ['Traffic congestion', 'Environmental regulation', 'Public investment', 'Urban development', 'Digital literacy'],
            'advanced' => ['What society values most', 'A rigorous methodology', 'Long-term policy planning', 'The available evidence', 'Sustainable economic growth'],
        ];
        $objects = [
            'foundation' => ['new words', 'short texts', 'basic grammar', 'simple questions', 'English sounds'],
            'elementary' => ['a simple plan', 'common mistakes', 'useful phrases', 'clear instructions', 'weekly goals'],
            'pre-intermediate' => ['the main idea', 'writing accuracy', 'exam strategies', 'supporting details', 'a balanced answer'],
            'intermediate' => ['student motivation', 'independent learning', 'academic vocabulary', 'complex sentences', 'reliable sources'],
            'upper-intermediate' => ['a balanced solution', 'public transport', 'economic pressure', 'environmental costs', 'policy outcomes'],
            'advanced' => ['substantial investment', 'a nuanced argument', 'long-term consequences', 'reliable empirical data', 'institutional reform'],
        ];
        $adjectives = [
            'foundation' => ['useful', 'clear', 'easy', 'short', 'important'],
            'elementary' => ['helpful', 'practical', 'simple', 'effective', 'necessary'],
            'pre-intermediate' => ['very helpful', 'well organised', 'more accurate', 'quite challenging', 'easy to follow'],
            'intermediate' => ['highly beneficial', 'increasingly popular', 'more persuasive', 'academically useful', 'carefully structured'],
            'upper-intermediate' => ['economically viable', 'socially responsible', 'highly controversial', 'widely accepted', 'difficult to implement'],
            'advanced' => ['methodologically sound', 'ethically complex', 'politically sensitive', 'statistically significant', 'hard to measure accurately'],
        ];
        $adverbs = [
            'foundation' => ['every day', 'at home', 'in class', 'after school', 'with care'],
            'elementary' => ['in the morning', 'after each lesson', 'with a partner', 'very often', 'before the test'],
            'pre-intermediate' => ['under time pressure', 'with clear notes', 'during revision', 'before submission', 'in a quiet place'],
            'intermediate' => ['over time', 'in academic contexts', 'with regular feedback', 'after careful analysis', 'during independent study'],
            'upper-intermediate' => ['after the reform', 'with limited resources', 'in major cities', 'under public pressure', 'during economic uncertainty'],
            'advanced' => ['with considerable caution', 'after extensive consultation', 'in the long term', 'under specific conditions', 'despite limited evidence'],
        ];
        $templates = [
            ['sentence' => '%s improves quickly.', 'target' => '%s', 'correct' => 'chủ ngữ', 'explanation' => '"%s" stands before the main verb and performs the action, so it is the subject.'],
            ['sentence' => 'Students review %s.', 'target' => 'review', 'correct' => 'vị ngữ', 'explanation' => '"%s" is the main verb, so it belongs to the predicate.'],
            ['sentence' => 'Learners need %s.', 'target' => '%s', 'correct' => 'tân ngữ', 'explanation' => '"%s" receives the action of the verb, so it is the object.'],
            ['sentence' => 'The answer is %s.', 'target' => '%s', 'correct' => 'bổ ngữ', 'explanation' => '"%s" follows a linking verb and describes the subject, so it is a complement.'],
            ['sentence' => 'Candidates practise speaking %s.', 'target' => '%s', 'correct' => 'trạng ngữ', 'explanation' => '"%s" adds time, place, manner, or condition information, so it is an adverbial.'],
        ];

        foreach (array_keys($subjects) as $level) {
            for ($i = 0; $i < self::QUESTIONS_PER_TEST; $i++) {
                $template = $templates[$i % count($templates)];
                $pool = match ($template['correct']) {
                    'chủ ngữ' => $subjects[$level],
                    'tân ngữ' => $objects[$level],
                    'bổ ngữ' => $adjectives[$level],
                    'trạng ngữ' => $adverbs[$level],
                    default => ['review'],
                };
                $term = $pool[$i % count($pool)];
                $sentenceTerm = $template['correct'] === 'chủ ngữ' ? $subjects[$level][$i % count($subjects[$level])] : $term;
                $target = sprintf($template['target'], $term);

                $questions[] = [
                    'level' => $level,
                    'sentence' => sprintf($template['sentence'], $sentenceTerm),
                    'target' => $target,
                    'correct' => $template['correct'],
                    'explanation' => sprintf($template['explanation'], $target),
                ];
            }
        }

        return $questions;
    }

    private function dictionaryEntriesForLevel(array $config)
    {
        return DictionaryEntry::whereIn('part_of_speech', ['noun', 'verb', 'adjective', 'adverb'])
            ->whereNotNull('definition')
            ->whereRaw('LENGTH(word) <= ?', [$config['max_word_length']])
            ->whereRaw('LENGTH(definition) <= ?', [$config['max_definition_length']]);
    }

    private function vocabularyWordsForLevel(array $config)
    {
        $words = Vocabulary::query()
            ->when($config['vocabulary_levels'], fn ($query) => $query->whereIn('level', $config['vocabulary_levels']))
            ->inRandomOrder()
            ->take($config['question_count'])
            ->get();

        if ($words->count() >= $config['question_count']) {
            return $words;
        }

        return $words->concat(
            Vocabulary::query()
                ->whereNotIn('id', $words->pluck('id'))
                ->inRandomOrder()
                ->take($config['question_count'] - $words->count())
                ->get()
        );
    }

    private function choiceOptions(string $correct, $pool)
    {
        $options = collect($pool)
            ->reject(fn ($option) => $option === $correct)
            ->shuffle()
            ->take(3)
            ->push($correct)
            ->unique()
            ->values();

        while ($options->count() < 4) {
            $options->push($correct . ' ' . ($options->count() + 1));
        }

        return $options->shuffle()->values();
    }

    private function spellingOptions(string $word)
    {
        $lettersOnly = preg_replace('/[^A-Za-z]/', '', $word) ?: $word;
        $variants = collect([
            $word,
            preg_replace('/[aeiou]/i', '', $lettersOnly, 1) ?: $word . 'e',
            strlen($lettersOnly) > 3 ? substr($lettersOnly, 1) . substr($lettersOnly, 0, 1) : $lettersOnly . 'e',
            strlen($lettersOnly) > 2 ? substr($lettersOnly, 0, 1) . substr($lettersOnly, 0, 1) . substr($lettersOnly, 1) : $lettersOnly . 's',
            str_replace(['c', 'C'], ['k', 'K'], $lettersOnly),
            str_replace(['s', 'S'], ['z', 'Z'], $lettersOnly),
        ])
            ->filter()
            ->unique()
            ->values();

        while ($variants->count() < 4) {
            $variants->push($word . $variants->count());
        }

        return $variants->take(4)->shuffle()->values();
    }

    private function blankExample(Vocabulary $word): string
    {
        $example = $word->example_en ?: "The word {$word->word} is useful in IELTS practice.";
        $blanked = preg_replace('/\b' . preg_quote($word->word, '/') . '\b/i', '_____', $example, 1);

        if ($blanked && $blanked !== $example) {
            return $blanked;
        }

        return "_____ - {$example}";
    }

    private function ieltsFormatQuestions(string $level): array
    {
        $profiles = [
            'foundation' => [
                ['topic' => 'daily routines', 'passage' => 'Many students build English confidence by reading short texts every day. A small habit is easier to keep than a long study session once a week.', 'main' => 'Small daily habits can improve language confidence.', 'detail' => 'reading short texts every day', 'inference' => 'Regular practice is more useful than rare long sessions.', 'heading' => 'Building a Daily Study Habit'],
                ['topic' => 'public transport', 'passage' => 'Buses and trains help people travel cheaply. They also reduce the number of private cars on busy roads.', 'main' => 'Public transport can save money and reduce traffic.', 'detail' => 'buses and trains', 'inference' => 'Using public transport can help crowded cities.', 'heading' => 'Why Shared Transport Matters'],
            ],
            'elementary' => [
                ['topic' => 'online learning', 'passage' => 'Online classes allow learners to study from home, but they still need a clear timetable. Without a plan, students may delay important tasks.', 'main' => 'Online learning is flexible but needs planning.', 'detail' => 'a clear timetable', 'inference' => 'Flexibility alone does not guarantee progress.', 'heading' => 'Planning for Online Study'],
                ['topic' => 'healthy habits', 'passage' => 'People who sleep well usually concentrate better. Exercise can also improve mood, especially when it becomes part of a weekly routine.', 'main' => 'Healthy habits support concentration and mood.', 'detail' => 'sleep well', 'inference' => 'Physical routines can affect study performance.', 'heading' => 'Health and Learning'],
            ],
            'pre-intermediate' => [
                ['topic' => 'city growth', 'passage' => 'As cities expand, local authorities must provide housing, transport and green spaces. Growth can create jobs, but it can also increase pressure on services.', 'main' => 'Urban growth brings both opportunities and pressure.', 'detail' => 'housing, transport and green spaces', 'inference' => 'City planning needs to balance economic and social needs.', 'heading' => 'Managing Expanding Cities'],
                ['topic' => 'work skills', 'passage' => 'Employers often value communication because technical knowledge is not enough on its own. Teams need people who can explain ideas clearly and listen carefully.', 'main' => 'Communication is an important workplace skill.', 'detail' => 'explain ideas clearly and listen carefully', 'inference' => 'Soft skills can support technical performance.', 'heading' => 'Communication at Work'],
            ],
            'intermediate' => [
                ['topic' => 'environmental policy', 'passage' => 'Many governments encourage recycling, but experts argue that reducing waste at the source has a stronger long-term effect. Consumers and companies both influence how much waste is produced.', 'main' => 'Waste reduction should happen before recycling becomes necessary.', 'detail' => 'consumers and companies', 'inference' => 'Responsibility for waste is shared by more than one group.', 'heading' => 'Beyond Recycling'],
                ['topic' => 'academic feedback', 'passage' => 'Feedback is most useful when it is specific. A general comment such as "improve your writing" may be less helpful than advice about paragraph structure or evidence.', 'main' => 'Specific feedback helps learners improve more effectively.', 'detail' => 'paragraph structure or evidence', 'inference' => 'Detailed comments are more actionable than broad advice.', 'heading' => 'Making Feedback Useful'],
            ],
            'upper-intermediate' => [
                ['topic' => 'technology and work', 'passage' => 'Automation can remove repetitive tasks, allowing employees to focus on analysis and creativity. However, workers may need training to adapt to new roles.', 'main' => 'Automation changes work rather than simply replacing it.', 'detail' => 'training', 'inference' => 'Education systems may need to respond to technological change.', 'heading' => 'Adapting to Automated Workplaces'],
                ['topic' => 'public health', 'passage' => 'Preventive healthcare is often cheaper than treating illness after it appears. Campaigns about diet, exercise and screening can reduce pressure on hospitals.', 'main' => 'Prevention can lower long-term healthcare costs.', 'detail' => 'diet, exercise and screening', 'inference' => 'Public information can be part of healthcare policy.', 'heading' => 'The Value of Prevention'],
            ],
            'advanced' => [
                ['topic' => 'research reliability', 'passage' => 'A study may be influential, yet its conclusions should be treated carefully if the sample is small or the method is unclear. Reliable research depends on transparent evidence and repeatable procedures.', 'main' => 'Research quality depends on method and evidence, not only influence.', 'detail' => 'transparent evidence and repeatable procedures', 'inference' => 'Popular findings still need critical evaluation.', 'heading' => 'Assessing Research Quality'],
                ['topic' => 'economic policy', 'passage' => 'Short-term subsidies can protect vulnerable groups, but they may become inefficient if they continue without review. Policy makers therefore need clear criteria for when support should change or end.', 'main' => 'Economic support should be reviewed against clear criteria.', 'detail' => 'clear criteria', 'inference' => 'Temporary policies can create problems if they become permanent without evaluation.', 'heading' => 'Reviewing Public Support'],
            ],
        ];
        $sourceLevels = $profiles[$level] ?? $profiles['intermediate'];
        $questionTypes = ['main', 'detail', 'completion', 'heading', 'inference'];
        $questions = [];

        for ($i = 0; $i < self::QUESTIONS_PER_TEST; $i++) {
            $profile = $sourceLevels[$i % count($sourceLevels)];
            $type = $questionTypes[$i % count($questionTypes)];
            $questions[] = $this->makeIeltsFormatQuestion($profile, $type, $i);
        }

        return $questions;
    }

    private function makeIeltsFormatQuestion(array $profile, string $type, int $index): array
    {
        $detail = "Passage: {$profile['passage']}";

        return match ($type) {
            'main' => [
                'prompt' => 'What is the main idea of the passage?',
                'detail' => $detail,
                'correct' => $profile['main'],
                'options' => $this->choiceOptions($profile['main'], collect([
                    $profile['main'],
                    'The passage only describes a personal problem.',
                    'The passage argues that change is never possible.',
                    'The passage focuses only on historical events.',
                    'The passage gives instructions for a laboratory experiment.',
                ])),
                'explanation' => 'Đáp án đúng tóm tắt ý chính của toàn đoạn, không chỉ một chi tiết nhỏ.',
            ],
            'detail' => [
                'prompt' => 'Which detail is mentioned in the passage?',
                'detail' => $detail,
                'correct' => $profile['detail'],
                'options' => $this->choiceOptions($profile['detail'], collect([
                    $profile['detail'],
                    'international tourism only',
                    'private entertainment',
                    'a single historical date',
                    'a medical experiment',
                ])),
                'explanation' => 'Đáp án đúng là chi tiết xuất hiện trực tiếp trong đoạn.',
            ],
            'completion' => [
                'prompt' => "Complete the sentence: The passage suggests that {$profile['topic']} is connected with _____.",
                'detail' => $detail,
                'correct' => $profile['main'],
                'options' => $this->choiceOptions($profile['main'], collect([
                    $profile['main'],
                    'a completely unrelated personal hobby',
                    'a fixed rule with no exceptions',
                    'a decline in all forms of education',
                    'a topic that the passage does not mention',
                ])),
                'explanation' => 'Câu hoàn thành phải phù hợp với nội dung và hướng lập luận của đoạn.',
            ],
            'heading' => [
                'prompt' => 'Choose the best heading for the passage.',
                'detail' => $detail,
                'correct' => $profile['heading'],
                'options' => $this->choiceOptions($profile['heading'], collect([
                    $profile['heading'],
                    'A List of Unrelated Facts',
                    'Why All Solutions Fail',
                    'A Story About One Traveller',
                    'The End of Modern Education',
                ])),
                'explanation' => 'Tiêu đề đúng bao quát nội dung chính của đoạn.',
            ],
            default => [
                'prompt' => 'What can be inferred from the passage?',
                'detail' => $detail,
                'correct' => $profile['inference'],
                'options' => $this->choiceOptions($profile['inference'], collect([
                    $profile['inference'],
                    'The writer rejects every possible solution.',
                    'The passage proves that the issue is simple.',
                    'Only one person is affected by the issue.',
                    'The topic is unrelated to real-life decisions.',
                ])),
                'explanation' => 'Đáp án đúng là suy luận hợp lý dựa trên thông tin trong đoạn, không phải chi tiết bị bịa thêm.',
            ],
        };
    }

    private function skillPracticeConfigs(): array
    {
        return [
            'true-false-not-given' => [
                'badge' => 'Đọc hiểu',
                'title' => 'True / False / Not Given',
                'subtitle' => 'Xác định thông tin đúng, sai hoặc không được nêu trong đoạn.',
                'description' => 'Luyện dạng True / False / Not Given theo đoạn đọc ngắn.',
                'history' => 'True False Not Given',
            ],
            'matching-headings' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Matching Headings',
                'subtitle' => 'Chọn tiêu đề phù hợp nhất cho đoạn văn.',
                'description' => 'Luyện chọn tiêu đề bao quát ý chính của đoạn.',
                'history' => 'Matching Headings',
            ],
            'sentence-completion' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Sentence Completion',
                'subtitle' => 'Hoàn thành câu dựa trên thông tin trong đoạn.',
                'description' => 'Luyện hoàn thành câu theo ngữ cảnh học thuật.',
                'history' => 'Sentence Completion',
            ],
            'summary-completion' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Summary Completion',
                'subtitle' => 'Chọn cụm từ phù hợp để hoàn thành phần tóm tắt.',
                'description' => 'Luyện tóm tắt ý chính và chọn thông tin còn thiếu.',
                'history' => 'Summary Completion',
            ],
            'matching-information' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Matching Information',
                'subtitle' => 'Xác định thông tin nằm ở phần nào của đoạn.',
                'description' => 'Luyện tìm vị trí thông tin và nối thông tin với đoạn.',
                'history' => 'Matching Information',
            ],
            'writer-purpose' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Writer Purpose',
                'subtitle' => 'Nhận diện mục đích của người viết trong đoạn văn.',
                'description' => 'Luyện suy luận mục đích viết và thái độ tác giả.',
                'history' => 'Writer Purpose',
            ],
            'paraphrase' => [
                'badge' => 'Từ vựng',
                'title' => 'Paraphrase Recognition',
                'subtitle' => 'Chọn cách diễn đạt lại đúng nghĩa.',
                'description' => 'Luyện nhận diện diễn đạt tương đương, rất hay gặp trong IELTS.',
                'history' => 'Paraphrase',
            ],
            'connectors' => [
                'badge' => 'Ngữ pháp',
                'title' => 'Linking Words',
                'subtitle' => 'Chọn từ nối phù hợp với quan hệ ý nghĩa.',
                'description' => 'Luyện however, therefore, although, because và các từ nối học thuật.',
                'history' => 'Linking Words',
            ],
            'collocation' => [
                'badge' => 'Từ vựng',
                'title' => 'Academic Collocations',
                'subtitle' => 'Chọn cụm từ tự nhiên trong văn phong học thuật.',
                'description' => 'Luyện các kết hợp từ phổ biến trong IELTS Writing và Reading.',
                'history' => 'Collocations',
            ],
            'word-family' => [
                'badge' => 'Từ vựng',
                'title' => 'Word Family',
                'subtitle' => 'Chọn dạng từ đúng theo vị trí trong câu.',
                'description' => 'Luyện danh từ, động từ, tính từ, trạng từ cùng gốc.',
                'history' => 'Word Family',
            ],
            'error-correction' => [
                'badge' => 'Ngữ pháp',
                'title' => 'Grammar Error Correction',
                'subtitle' => 'Chọn cách sửa lỗi ngữ pháp phù hợp nhất.',
                'description' => 'Luyện phát hiện và sửa lỗi thường gặp trong câu IELTS.',
                'history' => 'Error Correction',
            ],
            'reference-words' => [
                'badge' => 'Đọc hiểu',
                'title' => 'Reference Words',
                'subtitle' => 'Xác định đại từ hoặc cụm thay thế đang chỉ về đối tượng nào.',
                'description' => 'Luyện theo dõi mạch liên kết trong đoạn văn.',
                'history' => 'Reference Words',
            ],
        ];
    }

    private function skillPracticeConfig(string $skill): array
    {
        $configs = $this->skillPracticeConfigs();

        abort_if(! array_key_exists($skill, $configs), 404);

        return $configs[$skill];
    }

    private function skillPracticeQuestions(string $level, string $skill): array
    {
        $this->skillPracticeConfig($skill);
        $profiles = $this->skillPracticeProfiles($level);
        $questions = [];

        for ($i = 0; $i < self::QUESTIONS_PER_TEST; $i++) {
            $questions[] = $this->makeSkillPracticeQuestion($profiles[$i % count($profiles)], $skill, $i);
        }

        return $questions;
    }

    private function skillPracticeProfiles(string $level): array
    {
        $profiles = [
            'foundation' => [
                ['topic' => 'daily English practice', 'passage' => 'Learners improve when they practise a little every day. Short sessions are easier to repeat than very long lessons.', 'main' => 'Small daily practice is useful for learners.', 'detail' => 'short sessions', 'heading' => 'Daily Practice', 'purpose' => 'to explain why regular practice helps', 'reference' => 'they', 'referent' => 'learners', 'connector' => 'because', 'collocation' => 'make progress', 'word' => 'regular', 'family' => ['regular', 'regularly', 'regularity', 'regulate'], 'sentence' => 'Students improve when they practise _____.', 'wordAnswer' => 'regularly'],
                ['topic' => 'public transport', 'passage' => 'Public transport can reduce traffic in busy cities. It is also cheaper than using a private car every day.', 'main' => 'Public transport can be cheaper and reduce traffic.', 'detail' => 'busy cities', 'heading' => 'The Benefits of Public Transport', 'purpose' => 'to describe two benefits of public transport', 'reference' => 'It', 'referent' => 'public transport', 'connector' => 'also', 'collocation' => 'reduce traffic', 'word' => 'cheap', 'family' => ['cheap', 'cheaply', 'cheaper', 'cheapness'], 'sentence' => 'People can travel more _____ by bus.', 'wordAnswer' => 'cheaply'],
            ],
            'elementary' => [
                ['topic' => 'online learning', 'passage' => 'Online learning is flexible, but students need a timetable. A plan helps them avoid delaying important tasks.', 'main' => 'Online learning needs planning as well as flexibility.', 'detail' => 'a timetable', 'heading' => 'Planning Online Study', 'purpose' => 'to show why planning matters in online learning', 'reference' => 'them', 'referent' => 'students', 'connector' => 'but', 'collocation' => 'important tasks', 'word' => 'flexible', 'family' => ['flexible', 'flexibly', 'flexibility', 'flex'], 'sentence' => 'Online courses offer more _____.', 'wordAnswer' => 'flexibility'],
                ['topic' => 'healthy routines', 'passage' => 'Good sleep helps people concentrate. Regular exercise can also improve mood and energy.', 'main' => 'Healthy routines support concentration and mood.', 'detail' => 'regular exercise', 'heading' => 'Health and Study Performance', 'purpose' => 'to connect health habits with better study', 'reference' => 'also', 'referent' => 'an additional benefit', 'connector' => 'also', 'collocation' => 'improve mood', 'word' => 'concentrate', 'family' => ['concentrate', 'concentration', 'concentrated', 'concentrating'], 'sentence' => 'Good sleep improves _____.', 'wordAnswer' => 'concentration'],
            ],
            'pre-intermediate' => [
                ['topic' => 'city growth', 'passage' => 'As cities expand, governments must provide housing, transport and green spaces. Growth can create jobs, but it can also increase pressure on services.', 'main' => 'City growth creates both benefits and pressure.', 'detail' => 'housing, transport and green spaces', 'heading' => 'Managing Growing Cities', 'purpose' => 'to present advantages and challenges of urban growth', 'reference' => 'it', 'referent' => 'growth', 'connector' => 'but', 'collocation' => 'create jobs', 'word' => 'urban', 'family' => ['urban', 'urbanise', 'urbanisation', 'urbanised'], 'sentence' => 'Rapid _____ can put pressure on services.', 'wordAnswer' => 'urbanisation'],
                ['topic' => 'workplace skills', 'passage' => 'Employers value communication because teams need people who can explain ideas clearly. Technical knowledge alone is often not enough.', 'main' => 'Communication is important in the workplace.', 'detail' => 'explain ideas clearly', 'heading' => 'Communication at Work', 'purpose' => 'to explain the value of communication skills', 'reference' => 'who', 'referent' => 'people', 'connector' => 'because', 'collocation' => 'technical knowledge', 'word' => 'communicate', 'family' => ['communicate', 'communication', 'communicative', 'communicator'], 'sentence' => 'Clear _____ helps teams work better.', 'wordAnswer' => 'communication'],
            ],
            'intermediate' => [
                ['topic' => 'recycling', 'passage' => 'Recycling is helpful, yet reducing waste before it is created may have a stronger long-term effect. Companies and consumers both influence how much waste is produced.', 'main' => 'Reducing waste at the source can be more powerful than recycling alone.', 'detail' => 'companies and consumers', 'heading' => 'Beyond Recycling', 'purpose' => 'to compare recycling with waste reduction', 'reference' => 'it', 'referent' => 'waste', 'connector' => 'yet', 'collocation' => 'long-term effect', 'word' => 'reduce', 'family' => ['reduce', 'reduction', 'reduced', 'reducible'], 'sentence' => 'Waste _____ can have a long-term effect.', 'wordAnswer' => 'reduction'],
                ['topic' => 'feedback', 'passage' => 'Feedback is useful when it is specific. General advice may be less helpful than comments about structure, evidence or examples.', 'main' => 'Specific feedback is more useful than general advice.', 'detail' => 'structure, evidence or examples', 'heading' => 'Useful Feedback', 'purpose' => 'to show what makes feedback effective', 'reference' => 'it', 'referent' => 'feedback', 'connector' => 'when', 'collocation' => 'specific feedback', 'word' => 'specific', 'family' => ['specific', 'specifically', 'specificity', 'specify'], 'sentence' => 'Teachers should explain problems _____.', 'wordAnswer' => 'specifically'],
            ],
            'upper-intermediate' => [
                ['topic' => 'automation', 'passage' => 'Automation can remove repetitive tasks, allowing employees to focus on analysis and creativity. However, workers may need training to adapt to new roles.', 'main' => 'Automation changes the type of work people do.', 'detail' => 'training', 'heading' => 'Adapting to Automation', 'purpose' => 'to explain both opportunity and adjustment in automated workplaces', 'reference' => 'However', 'referent' => 'a contrast with the previous benefit', 'connector' => 'however', 'collocation' => 'repetitive tasks', 'word' => 'analyse', 'family' => ['analyse', 'analysis', 'analytical', 'analytically'], 'sentence' => 'Employees may focus more on _____.', 'wordAnswer' => 'analysis'],
                ['topic' => 'preventive healthcare', 'passage' => 'Preventive healthcare is often cheaper than treating illness after it appears. Campaigns about diet, exercise and screening can reduce pressure on hospitals.', 'main' => 'Prevention can reduce healthcare costs and hospital pressure.', 'detail' => 'diet, exercise and screening', 'heading' => 'The Value of Prevention', 'purpose' => 'to argue that prevention is useful in public health', 'reference' => 'it', 'referent' => 'illness', 'connector' => 'than', 'collocation' => 'reduce pressure', 'word' => 'prevent', 'family' => ['prevent', 'prevention', 'preventive', 'preventable'], 'sentence' => '_____ healthcare can be cheaper in the long term.', 'wordAnswer' => 'Preventive'],
            ],
            'advanced' => [
                ['topic' => 'research quality', 'passage' => 'A study may be influential, yet its conclusions should be treated carefully if the sample is small or the method is unclear. Reliable research depends on transparent evidence and repeatable procedures.', 'main' => 'Research quality depends on evidence and method.', 'detail' => 'transparent evidence and repeatable procedures', 'heading' => 'Assessing Research Quality', 'purpose' => 'to encourage critical evaluation of research', 'reference' => 'its', 'referent' => 'a study', 'connector' => 'yet', 'collocation' => 'transparent evidence', 'word' => 'rely', 'family' => ['rely', 'reliable', 'reliability', 'reliably'], 'sentence' => 'Researchers need to check the _____ of the evidence.', 'wordAnswer' => 'reliability'],
                ['topic' => 'economic policy', 'passage' => 'Short-term subsidies can protect vulnerable groups, but they may become inefficient if they continue without review. Policy makers need clear criteria for when support should change or end.', 'main' => 'Public support should be reviewed with clear criteria.', 'detail' => 'clear criteria', 'heading' => 'Reviewing Public Support', 'purpose' => 'to explain why policy review is necessary', 'reference' => 'they', 'referent' => 'subsidies', 'connector' => 'but', 'collocation' => 'vulnerable groups', 'word' => 'efficient', 'family' => ['efficient', 'efficiently', 'efficiency', 'inefficient'], 'sentence' => 'A policy may lose its _____ over time.', 'wordAnswer' => 'efficiency'],
            ],
        ];

        return $profiles[$level] ?? $profiles['intermediate'];
    }

    private function makeSkillPracticeQuestion(array $profile, string $skill, int $index): array
    {
        $detail = "Đoạn đọc: {$profile['passage']}";

        return match ($skill) {
            'true-false-not-given' => $this->trueFalseNotGivenQuestion($profile, $index, $detail),
            'matching-headings' => [
                'prompt' => 'Chọn tiêu đề phù hợp nhất cho đoạn văn.',
                'detail' => $detail,
                'correct' => $profile['heading'],
                'options' => $this->choiceOptions($profile['heading'], collect([$profile['heading'], 'Một câu chuyện cá nhân', 'Một danh sách số liệu', 'Một vấn đề không có giải pháp', 'Một hướng dẫn kỹ thuật'])),
                'explanation' => 'Tiêu đề đúng bao quát ý chính của toàn đoạn.',
            ],
            'sentence-completion' => [
                'prompt' => "Hoàn thành câu: The passage suggests that {$profile['topic']} is mainly about _____.",
                'detail' => $detail,
                'correct' => $profile['main'],
                'options' => $this->choiceOptions($profile['main'], collect([$profile['main'], 'a single private hobby', 'an unrelated historical fact', 'a topic not mentioned in the passage', 'a laboratory instruction'])),
                'explanation' => 'Câu hoàn thành phải khớp với nội dung chính của đoạn.',
            ],
            'summary-completion' => [
                'prompt' => 'Chọn cụm từ phù hợp để hoàn thành tóm tắt: The writer focuses on _____.',
                'detail' => $detail,
                'correct' => $profile['main'],
                'options' => $this->choiceOptions($profile['main'], collect([$profile['main'], 'details that are not in the text', 'only one personal example', 'a completely opposite claim', 'a definition of one simple word'])),
                'explanation' => 'Tóm tắt đúng phải giữ lại ý trọng tâm của đoạn.',
            ],
            'matching-information' => [
                'prompt' => 'Thông tin nào xuất hiện trong đoạn?',
                'detail' => $detail,
                'correct' => $profile['detail'],
                'options' => $this->choiceOptions($profile['detail'], collect([$profile['detail'], 'a fixed exam date', 'a personal travel story', 'a list of countries', 'a laboratory result'])),
                'explanation' => 'Đáp án đúng là chi tiết được nêu trực tiếp trong đoạn.',
            ],
            'writer-purpose' => [
                'prompt' => 'Mục đích chính của người viết là gì?',
                'detail' => $detail,
                'correct' => $profile['purpose'],
                'options' => $this->choiceOptions($profile['purpose'], collect([$profile['purpose'], 'to entertain with a fictional story', 'to sell a product directly', 'to describe a personal holiday', 'to reject all possible solutions'])),
                'explanation' => 'Mục đích đúng phản ánh chức năng giao tiếp của đoạn.',
            ],
            'paraphrase' => [
                'prompt' => "Chọn cách diễn đạt lại gần nghĩa nhất: {$profile['main']}",
                'detail' => $detail,
                'correct' => "In other words, {$profile['main']}",
                'options' => $this->choiceOptions("In other words, {$profile['main']}", collect(["In other words, {$profile['main']}", 'The opposite idea is more accurate.', 'The passage does not discuss this issue.', 'Only one minor example matters here.', 'This means the topic is unrelated to study.'])),
                'explanation' => 'Cách diễn đạt lại đúng giữ nguyên nghĩa cốt lõi.',
            ],
            'connectors' => [
                'prompt' => 'Chọn từ nối phù hợp nhất với quan hệ ý trong đoạn.',
                'detail' => "Câu gợi ý có quan hệ ý tương tự đoạn đọc về {$profile['topic']}.",
                'correct' => $profile['connector'],
                'options' => $this->choiceOptions($profile['connector'], collect([$profile['connector'], 'although', 'therefore', 'for example', 'meanwhile'])),
                'explanation' => 'Từ nối đúng thể hiện đúng quan hệ nguyên nhân, bổ sung hoặc tương phản.',
            ],
            'collocation' => [
                'prompt' => 'Chọn cụm từ tự nhiên nhất trong văn phong học thuật.',
                'detail' => "Chủ đề: {$profile['topic']}",
                'correct' => $profile['collocation'],
                'options' => $this->choiceOptions($profile['collocation'], collect([$profile['collocation'], 'do a progress', 'make an evidence', 'strongly traffic', 'highly homework'])),
                'explanation' => 'Đáp án đúng là kết hợp từ tự nhiên trong tiếng Anh.',
            ],
            'word-family' => [
                'prompt' => $profile['sentence'],
                'detail' => 'Chọn dạng từ đúng theo vị trí ngữ pháp.',
                'correct' => $profile['wordAnswer'],
                'options' => $this->choiceOptions($profile['wordAnswer'], collect($profile['family'])),
                'explanation' => 'Dạng từ đúng phù hợp với vai trò ngữ pháp trong câu.',
            ],
            'error-correction' => [
                'prompt' => "Chọn cách sửa đúng: {$profile['sentence']}",
                'detail' => 'Câu cần dùng đúng dạng từ hoặc cụm từ học thuật.',
                'correct' => str_replace('_____', $profile['wordAnswer'], $profile['sentence']),
                'options' => $this->choiceOptions(str_replace('_____', $profile['wordAnswer'], $profile['sentence']), collect([
                    str_replace('_____', $profile['wordAnswer'], $profile['sentence']),
                    str_replace('_____', $profile['word'], $profile['sentence']),
                    str_replace('_____', $profile['family'][0], $profile['sentence']),
                    str_replace('_____', $profile['family'][count($profile['family']) - 1], $profile['sentence']),
                ])),
                'explanation' => 'Câu đúng dùng dạng từ phù hợp với cấu trúc.',
            ],
            'reference-words' => [
                'prompt' => "Trong đoạn, \"{$profile['reference']}\" chỉ về đối tượng nào?",
                'detail' => $detail,
                'correct' => $profile['referent'],
                'options' => $this->choiceOptions($profile['referent'], collect([$profile['referent'], 'the reader', 'a country not mentioned', 'a past exam', 'an unrelated example'])),
                'explanation' => 'Đáp án đúng là đối tượng mà từ tham chiếu thay thế trong mạch văn.',
            ],
            default => abort(404),
        };
    }

    private function trueFalseNotGivenQuestion(array $profile, int $index, string $detail): array
    {
        $variants = [
            ['statement' => $profile['main'], 'answer' => 'True', 'explanation' => 'Thông tin này phù hợp với ý của đoạn.'],
            ['statement' => 'The passage says the issue has no connection with real life.', 'answer' => 'False', 'explanation' => 'Thông tin này trái với nội dung đoạn.'],
            ['statement' => 'The passage gives the exact number of people in the study.', 'answer' => 'Not Given', 'explanation' => 'Đoạn không nêu con số chính xác này.'],
        ];
        $variant = $variants[$index % count($variants)];

        return [
            'prompt' => "Statement: {$variant['statement']}",
            'detail' => $detail,
            'correct' => $variant['answer'],
            'options' => collect(['True', 'False', 'Not Given']),
            'explanation' => $variant['explanation'],
        ];
    }

    private function recordAttempt(string $testType, string $level, int $score, int $total, array $results): void
    {
        if (! auth()->check()) {
            return;
        }

        TestAttempt::create([
            'user_id' => auth()->id(),
            'test_type' => $testType,
            'level' => $this->levelConfig($level)['name'],
            'score' => $score,
            'total' => $total,
            'details' => $results,
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function validatedAnswers(Request $request): array
    {
        return $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'string', 'max:1000'],
        ])['answers'];
    }

    /**
     * @return array{answers: array<string, string>, correct: array<string, string>, explanations: array<string, string>, targets: array<string, string>}
     */
    private function validatedSentenceRolePayload(Request $request): array
    {
        return $request->validate([
            'answers' => ['required', 'array', 'min:1'],
            'answers.*' => ['required', 'string', 'max:1000'],
            'correct' => ['required', 'array', 'min:1'],
            'correct.*' => ['required', 'string', 'max:1000'],
            'explanations' => ['required', 'array', 'min:1'],
            'explanations.*' => ['required', 'string', 'max:2000'],
            'targets' => ['required', 'array', 'min:1'],
            'targets.*' => ['required', 'string', 'max:500'],
        ]);
    }

    private function grammarReason(string $pos): string
    {
        return [
            'noun' => 'Danh từ thường làm chủ ngữ, tân ngữ hoặc dùng sau giới từ.',
            'verb' => 'Động từ thường làm trung tâm vị ngữ và diễn tả hành động/trạng thái.',
            'adjective' => 'Tính từ thường bổ nghĩa cho danh từ hoặc dùng sau be/seem/become.',
            'adverb' => 'Trạng từ thường bổ nghĩa cho động từ, tính từ, trạng từ khác hoặc cả câu.',
        ][$pos] ?? 'Cần xem ngữ cảnh để xác định vai trò.';
    }

    private function levels(): array
    {
        return [
            'foundation' => [
                'name' => 'Cấp 1 - Foundation',
                'band' => 'IELTS 0 - 3.0',
                'description' => 'Làm quen từ cơ bản, câu đơn, chủ ngữ - động từ - tân ngữ.',
                'vocabulary_levels' => ['B1'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 7,
                'max_definition_length' => 80,
                'sentence_levels' => ['foundation'],
            ],
            'elementary' => [
                'name' => 'Cấp 2 - Elementary',
                'band' => 'IELTS 3.0 - 4.0',
                'description' => 'Từ vựng thông dụng, loại từ cơ bản, câu đơn mở rộng.',
                'vocabulary_levels' => ['B1'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 9,
                'max_definition_length' => 100,
                'sentence_levels' => ['foundation', 'elementary'],
            ],
            'pre-intermediate' => [
                'name' => 'Cấp 3 - Pre-Intermediate',
                'band' => 'IELTS 4.0 - 5.0',
                'description' => 'Từ vựng IELTS nền tảng, câu có cụm danh từ và động từ khuyết thiếu.',
                'vocabulary_levels' => ['B1', 'B2'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 11,
                'max_definition_length' => 130,
                'sentence_levels' => ['elementary', 'pre-intermediate'],
            ],
            'intermediate' => [
                'name' => 'Cấp 4 - Intermediate',
                'band' => 'IELTS 5.0 - 6.0',
                'description' => 'Từ học thuật phổ biến, nhận diện bổ ngữ, trạng ngữ và câu dài hơn.',
                'vocabulary_levels' => ['B1', 'B2', 'IELTS'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 13,
                'max_definition_length' => 170,
                'sentence_levels' => ['pre-intermediate', 'intermediate'],
            ],
            'upper-intermediate' => [
                'name' => 'Cấp 5 - Upper-Intermediate',
                'band' => 'IELTS 6.0 - 7.0',
                'description' => 'Từ học thuật khó hơn, câu phức, cụm từ làm thành phần câu.',
                'vocabulary_levels' => ['B2', 'C1', 'IELTS'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 16,
                'max_definition_length' => 220,
                'sentence_levels' => ['intermediate', 'upper-intermediate'],
            ],
            'advanced' => [
                'name' => 'Cấp 6 - Advanced',
                'band' => 'IELTS 7.0+',
                'description' => 'Từ học thuật nâng cao, câu phức và mệnh đề danh từ/tính từ/trạng từ.',
                'vocabulary_levels' => ['C1', 'IELTS'],
                'question_count' => self::QUESTIONS_PER_TEST,
                'max_word_length' => 24,
                'max_definition_length' => 320,
                'sentence_levels' => ['upper-intermediate', 'advanced'],
            ],
        ];
    }

    private function levelConfig(string $level): array
    {
        abort_if(! array_key_exists($level, $this->levels()), 404);

        return $this->levels()[$level];
    }
}
