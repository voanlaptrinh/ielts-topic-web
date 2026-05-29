<?php

namespace App\Http\Controllers;

use App\Models\PracticeTest;
use App\Models\Topic;
use App\Models\Vocabulary;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = trim((string) $request->query('q'));

        return view('search.index', [
            'query' => $query,
            'prepMatches' => $this->prepMatches($query),
            'topics' => $query === '' ? collect() : Topic::where('title', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%")
                ->take(8)
                ->get(),
            'vocabularies' => $query === '' ? collect() : Vocabulary::where('word', 'like', "%{$query}%")
                ->orWhere('meaning_vi', 'like', "%{$query}%")
                ->orWhere('topic', 'like', "%{$query}%")
                ->take(12)
                ->get(),
            'practiceTests' => $query === '' ? collect() : PracticeTest::where('is_published', true)
                ->where(function ($builder) use ($query) {
                    $builder->where('title', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%")
                        ->orWhere('level', 'like', "%{$query}%")
                        ->orWhere('skill', 'like', "%{$query}%");
                })
                ->take(12)
                ->get(),
        ]);
    }

    private function prepMatches(string $query): array
    {
        if ($query === '') {
            return [];
        }

        return collect([
            ['title' => 'IELTS Prep Hub', 'description' => 'Study plan, band criteria, common mistakes và mock checklist.', 'route' => route('prep.index')],
            ['title' => 'Writing band criteria', 'description' => 'Task response, coherence, lexical resource, grammar range.', 'route' => route('prep.index')],
            ['title' => 'Speaking band criteria', 'description' => 'Fluency, vocabulary, grammar và pronunciation.', 'route' => route('prep.index')],
            ['title' => 'Mock test checklist', 'description' => 'Làm đề dưới điều kiện thời gian thật và review lỗi sai.', 'route' => route('prep.index')],
        ])
            ->filter(fn ($item) => str_contains(strtolower($item['title'] . ' ' . $item['description']), strtolower($query)))
            ->values()
            ->all();
    }
}
