<?php

namespace App\Http\Controllers;

use App\Models\DictionaryEntry;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\Vocabulary;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::orderBy('part')->orderBy('title')->get();
        $stats = [
            'topics' => Topic::count(),
            'vocabularies' => Vocabulary::count(),
            'dictionary_words' => DictionaryEntry::distinct('normalized_word')->count('normalized_word'),
        ];
        $recentAttempts = auth()->check()
            ? TestAttempt::where('user_id', auth()->id())->latest()->take(3)->get()
            : collect();

        return view('topics.index', compact('topics', 'stats', 'recentAttempts'));
    }

    public function show(string $slug)
    {
        $topic = Topic::where('slug', $slug)->firstOrFail();

        return view('topics.show', compact('topic'));
    }
}
