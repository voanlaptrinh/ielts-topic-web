<?php

namespace App\Http\Controllers;

use App\Models\DictionaryEntry;
use App\Models\WordLookupHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DictionaryController extends Controller
{
    public function index(Request $request)
    {
        $search = strtolower(trim((string) $request->query('q')));

        $words = DictionaryEntry::query()
            ->select([
                'normalized_word',
                DB::raw('MIN(word) as word'),
                DB::raw('COUNT(*) as senses_count'),
                DB::raw('GROUP_CONCAT(DISTINCT part_of_speech) as parts_of_speech'),
            ])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('normalized_word', $search)
                        ->orWhere('normalized_word', 'like', $search . '%')
                        ->orWhere('definition', 'like', '%' . $search . '%');
                });
            })
            ->groupBy('normalized_word')
            ->orderByRaw($search ? 'CASE WHEN normalized_word = ? THEN 0 ELSE 1 END' : 'normalized_word', $search ? [$search] : [])
            ->orderBy('normalized_word')
            ->paginate(50)
            ->withQueryString();

        return view('dictionary.index', compact('words', 'search'));
    }

    public function show(string $word)
    {
        $normalizedWord = strtolower(str_replace('_', ' ', $word));

        $entries = DictionaryEntry::where('normalized_word', $normalizedWord)
            ->orderBy('part_of_speech')
            ->orderBy('id')
            ->get();

        abort_if($entries->isEmpty(), 404);

        if (auth()->check()) {
            WordLookupHistory::create([
                'user_id' => auth()->id(),
                'word' => $entries->first()->word,
                'normalized_word' => $normalizedWord,
                'senses_count' => $entries->count(),
            ]);
        }

        return view('dictionary.show', [
            'word' => $entries->first()->word,
            'entries' => $entries,
            'grammarNotes' => $entries->mapWithKeys(fn ($entry) => [
                $entry->id => $this->grammarNote($entry->part_of_speech),
            ]),
        ]);
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
