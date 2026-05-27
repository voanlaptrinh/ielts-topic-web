<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $attempts = $user->testAttempts()->latest()->take(50)->get();
        $lookups = $user->wordLookupHistories()->latest()->take(50)->get();
        $totalQuestions = $attempts->sum('total');
        $totalScore = $attempts->sum('score');

        return view('history.index', [
            'lookups' => $lookups,
            'attempts' => $attempts,
            'summary' => [
                'attempts' => $attempts->count(),
                'lookups' => $lookups->count(),
                'accuracy' => $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100) : 0,
                'wrong_count' => max(0, $totalQuestions - $totalScore),
            ],
        ]);
    }
}
