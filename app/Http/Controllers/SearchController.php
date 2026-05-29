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
}
