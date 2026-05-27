<?php

namespace App\Http\Controllers;

use App\Models\DictionaryEntry;
use App\Models\Vocabulary;
use App\Models\WordLookupHistory;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DictionaryController extends Controller
{
    private static ?array $exceptionMap = null;

    public function index(Request $request)
    {
        return view('dictionary.index', $this->searchData($request));
    }

    public function search(Request $request)
    {
        return view('dictionary._results', $this->searchData($request));
    }

    public function translate(Request $request)
    {
        $payload = $request->validate([
            'q' => ['required', 'string', 'max:5000'],
            'target' => ['nullable', 'string', 'size:2'],
            'source' => ['nullable', 'string', 'size:2'],
        ]);

        $apiKey = config('services.google_translate.key');

        if (! $apiKey) {
            return response()->json([
                'message' => 'Chưa cấu hình GOOGLE_TRANSLATE_API_KEY trong .env.',
            ], 503);
        }

        $body = [
            'q' => $payload['q'],
            'target' => $payload['target'] ?? 'vi',
            'format' => 'text',
        ];

        if (! empty($payload['source'])) {
            $body['source'] = $payload['source'];
        }

        $response = Http::timeout(12)
            ->acceptJson()
            ->post(config('services.google_translate.endpoint') . '?key=' . urlencode($apiKey), $body);

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Google Translate API chưa trả về bản dịch. Kiểm tra API key, billing hoặc quyền Cloud Translation API.',
            ], 502);
        }

        $translation = $response->json('data.translations.0.translatedText');
        $detectedSource = $response->json('data.translations.0.detectedSourceLanguage');

        if (! is_string($translation) || $translation === '') {
            return response()->json([
                'message' => 'Không đọc được kết quả dịch từ Google Translate API.',
            ], 502);
        }

        return response()->json([
            'translatedText' => html_entity_decode($translation, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'detectedSourceLanguage' => $detectedSource,
        ]);
    }

    private function searchData(Request $request): array
    {
        $search = $this->normalizeSearch((string) $request->query('q'));
        $candidates = $this->lookupCandidates($search);
        $words = $search === ''
            ? new LengthAwarePaginator([], 0, 50)
            : DictionaryEntry::query()
            ->select([
                'normalized_word',
                DB::raw('MIN(word) as word'),
                DB::raw('COUNT(*) as senses_count'),
                DB::raw('GROUP_CONCAT(DISTINCT part_of_speech) as parts_of_speech'),
            ])
            ->when($search, function ($query) use ($search, $candidates) {
                $query->where(function ($query) use ($search, $candidates) {
                    $query->whereIn('normalized_word', $candidates)
                        ->orWhere('normalized_word', 'like', $search . '%')
                        ->orWhere('definition', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('normalized_word')
            ->orderByRaw(
                $search ? 'CASE WHEN normalized_word IN (' . $this->placeholders($candidates) . ') THEN 0 ELSE 1 END' : 'normalized_word',
                $search ? $candidates : []
            )
            ->orderBy('normalized_word')
            ->paginate(50)
            ->withQueryString();

        $exactEntries = $search
            ? DictionaryEntry::whereIn('normalized_word', $candidates)
                ->orderByRaw($this->caseOrderSql('normalized_word', $candidates), $candidates)
                ->orderBy('part_of_speech')
                ->take(8)
                ->get()
            : collect();

        $vocabularyMatches = $search
            ? Vocabulary::whereIn(DB::raw('LOWER(word)'), $candidates)
                ->orWhere('word', 'like', $search . '%')
                ->orderByRaw('CASE WHEN LOWER(word) IN (' . $this->placeholders($candidates) . ') THEN 0 ELSE 1 END', $candidates)
                ->orderBy('word')
                ->take(6)
                ->get()
            : collect();

        $suggestions = collect();
        if ($search && $words->isEmpty()) {
            $suggestions = DictionaryEntry::query()
                ->select([
                    'normalized_word',
                    DB::raw('MIN(word) as word'),
                    DB::raw('COUNT(*) as senses_count'),
                ])
                ->where(function ($query) use ($search) {
                    $prefix = mb_substr($search, 0, max(2, min(4, mb_strlen($search))));
                    $query->where('normalized_word', 'like', $prefix . '%')
                        ->orWhere('definition', 'like', '%' . $search . '%');
                })
                ->groupBy('normalized_word')
                ->orderBy('normalized_word')
                ->take(12)
                ->get();
        }

        return [
            'words' => $words,
            'search' => $search,
            'exactEntries' => $exactEntries,
            'vocabularyMatches' => $vocabularyMatches,
            'suggestions' => $suggestions,
            'resolvedWord' => $vocabularyMatches->first()?->word ? mb_strtolower($vocabularyMatches->first()->word) : $exactEntries->first()?->normalized_word,
        ];
    }

    public function show(string $word)
    {
        $searchedWord = $this->normalizeSearch(str_replace('_', ' ', $word));
        $candidates = $this->lookupCandidates($searchedWord);

        $entries = DictionaryEntry::whereIn('normalized_word', $candidates)
            ->orderByRaw($this->caseOrderSql('normalized_word', $candidates), $candidates)
            ->orderBy('part_of_speech')
            ->orderBy('id')
            ->get();

        abort_if($entries->isEmpty(), 404);

        if (auth()->check()) {
            WordLookupHistory::create([
                'user_id' => auth()->id(),
                'word' => $entries->first()->word,
                'normalized_word' => $entries->first()->normalized_word,
                'senses_count' => $entries->count(),
            ]);
        }

        return view('dictionary.show', [
            'word' => $entries->first()->word,
            'searchedWord' => $searchedWord,
            'resolvedWord' => $entries->first()->normalized_word,
            'entries' => $entries,
            'relatedWords' => $this->relatedWords($entries),
            'vocabulary' => Vocabulary::whereIn('word', $candidates)->first(),
            'grammarNotes' => $entries->mapWithKeys(fn ($entry) => [
                $entry->id => $this->grammarNote($entry->part_of_speech),
            ]),
        ]);
    }

    private function normalizeSearch(string $value): string
    {
        $value = str_replace(['_', '-'], ' ', $value);
        $value = preg_replace('/[^\pL\pN\s\']+/u', ' ', $value) ?? $value;
        $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

        return mb_strtolower(trim($value));
    }

    /**
     * @return array<int, string>
     */
    private function lookupCandidates(string $word): array
    {
        if ($word === '') {
            return [];
        }

        $exceptionMap = $this->exceptionMap();
        $candidates = [$word];

        if (isset($exceptionMap[$word])) {
            $candidates = array_merge($candidates, $exceptionMap[$word]);
        }

        $candidates = array_merge($candidates, $this->simpleLemmas($word));

        return collect($candidates)
            ->map(fn ($candidate) => $this->normalizeSearch($candidate))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * @return array<string, array<int, string>>
     */
    private function exceptionMap(): array
    {
        if (self::$exceptionMap !== null) {
            return self::$exceptionMap;
        }

        $map = [];
        foreach (['noun.exc', 'verb.exc', 'adj.exc', 'adv.exc'] as $file) {
            $path = database_path('seeders/data/oewn2025/' . $file);
            if (! is_readable($path)) {
                continue;
            }

            foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [] as $line) {
                $parts = preg_split('/\s+/', trim($line));
                if (! is_array($parts) || count($parts) < 2) {
                    continue;
                }

                $inflected = $this->normalizeSearch(array_shift($parts));
                $map[$inflected] = array_values(array_unique(array_merge($map[$inflected] ?? [], $parts)));
            }
        }

        return self::$exceptionMap = $map;
    }

    /**
     * @return array<int, string>
     */
    private function simpleLemmas(string $word): array
    {
        $lemmas = [];

        if (str_ends_with($word, 'ies') && mb_strlen($word) > 4) {
            $lemmas[] = mb_substr($word, 0, -3) . 'y';
        }

        if (str_ends_with($word, 'ing') && mb_strlen($word) > 5) {
            $stem = mb_substr($word, 0, -3);
            $lemmas[] = $stem;
            $lemmas[] = preg_replace('/(.)\1$/u', '$1', $stem) ?? $stem;
        }

        if (str_ends_with($word, 'ed') && mb_strlen($word) > 4) {
            $stem = mb_substr($word, 0, -2);
            $lemmas[] = $stem;
            $lemmas[] = preg_replace('/(.)\1$/u', '$1', $stem) ?? $stem;
        }

        if (str_ends_with($word, 'es') && mb_strlen($word) > 4) {
            $lemmas[] = mb_substr($word, 0, -2);
        }

        if (str_ends_with($word, 's') && mb_strlen($word) > 3) {
            $lemmas[] = mb_substr($word, 0, -1);
        }

        return $lemmas;
    }

    /**
     * @param Collection<int, DictionaryEntry> $entries
     * @return Collection<int, array{word: string, url: string}>
     */
    private function relatedWords(Collection $entries): Collection
    {
        return $entries
            ->flatMap(fn (DictionaryEntry $entry) => $entry->synonyms ?? [])
            ->map(fn ($word) => $this->normalizeSearch((string) $word))
            ->filter()
            ->unique()
            ->take(20)
            ->map(fn ($word) => [
                'word' => $word,
                'url' => route('dictionary.show', str_replace(' ', '_', $word)),
            ])
            ->values();
    }

    /**
     * @param array<int, string> $values
     */
    private function placeholders(array $values): string
    {
        return implode(', ', array_fill(0, max(1, count($values)), '?'));
    }

    /**
     * @param array<int, string> $values
     */
    private function caseOrderSql(string $column, array $values): string
    {
        if ($values === []) {
            return $column;
        }

        $cases = collect($values)
            ->map(fn ($value, $index) => 'WHEN ? THEN ' . $index)
            ->implode(' ');

        return 'CASE ' . $column . ' ' . $cases . ' ELSE 999 END';
    }

    private function grammarNote(string $partOfSpeech): array
    {
        return match ($partOfSpeech) {
            'noun' => [
                'label' => 'Danh từ',
                'role' => 'Thường làm chủ ngữ, tân ngữ, bổ ngữ sau động từ be, hoặc dùng sau giới từ.',
                'pattern' => 'The + noun + verb; adjective + noun; preposition + noun.',
            ],
            'verb' => [
                'label' => 'Động từ',
                'role' => 'Thường làm vị ngữ trong câu, diễn tả hành động, trạng thái hoặc quá trình.',
                'pattern' => 'Subject + verb; Subject + verb + object; Subject + be + verb-ing.',
            ],
            'adjective' => [
                'label' => 'Tính từ',
                'role' => 'Thường bổ nghĩa cho danh từ hoặc dùng sau be/seem/become để mô tả chủ ngữ.',
                'pattern' => 'adjective + noun; Subject + be/seem/become + adjective.',
            ],
            'adverb' => [
                'label' => 'Trạng từ',
                'role' => 'Thường bổ nghĩa cho động từ, tính từ, trạng từ khác hoặc cả câu.',
                'pattern' => 'verb + adverb; adverb + adjective; adverb + sentence.',
            ],
            default => [
                'label' => ucfirst($partOfSpeech),
                'role' => 'Vai trò phụ thuộc vào ngữ cảnh câu.',
                'pattern' => 'Cần xem ví dụ để xác định cách dùng.',
            ],
        };
    }
}
