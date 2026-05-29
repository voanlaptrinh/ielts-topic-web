<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $attempts = $user->testAttempts()->latest()->take(12)->get();
        $lookups = $user->wordLookupHistories()->latest()->take(10)->get();
        $totalQuestions = $attempts->sum('total');
        $totalScore = $attempts->sum('score');
        $wrongItems = $attempts
            ->flatMap(fn ($attempt) => collect($attempt->details ?? [])
                ->reject(fn ($detail) => $detail['is_correct'] ?? false)
                ->map(fn ($detail) => [
                    'test_type' => $attempt->test_type,
                    'level' => $attempt->level,
                    'word' => $detail['word'] ?? 'Câu hỏi',
                    'answer' => $detail['answer'] ?? 'Chưa chọn',
                    'correct' => $detail['correct'] ?? '',
                    'explanation' => $detail['explanation'] ?? '',
                ]))
            ->take(8)
            ->values();

        $accuracy = $totalQuestions > 0 ? round(($totalScore / $totalQuestions) * 100) : 0;
        $recommendedLevel = $accuracy < 55 ? 'pre-intermediate' : ($accuracy < 75 ? 'intermediate' : 'upper-intermediate');
        $skillBreakdown = $attempts
            ->groupBy('test_type')
            ->map(function ($items, $skill) {
                $total = $items->sum('total');

                return [
                    'skill' => $skill,
                    'attempts' => $items->count(),
                    'accuracy' => $total > 0 ? round(($items->sum('score') / $total) * 100) : 0,
                ];
            })
            ->sortBy('accuracy')
            ->values();
        $weakestSkill = $skillBreakdown->first()['skill'] ?? 'IELTS Reading';

        return view('dashboard.index', [
            'attempts' => $attempts,
            'lookups' => $lookups,
            'wrongItems' => $wrongItems,
            'recommendedLevel' => $recommendedLevel,
            'skillBreakdown' => $skillBreakdown,
            'weakestSkill' => $weakestSkill,
            'roadmap' => $this->roadmapFor($user->target_band ?: '6.5'),
            'summary' => [
                'attempts' => $attempts->count(),
                'accuracy' => $accuracy,
                'wrong_count' => max(0, $totalQuestions - $totalScore),
                'lookups' => $lookups->count(),
            ],
        ]);
    }

    private function roadmapFor(string $band): array
    {
        return [
            ['label' => 'Tuần 1', 'task' => 'Làm chẩn đoán 4 kỹ năng và ôn lỗi sai nhiều nhất.'],
            ['label' => 'Tuần 2', 'task' => 'Tập trung Reading/Listening theo dạng câu yếu.'],
            ['label' => 'Tuần 3', 'task' => 'Viết 2 bài Writing và luyện 3 topic Speaking.'],
            ['label' => 'Tuần 4', 'task' => "Làm mock test, so sánh với mục tiêu band {$band} và chỉnh lộ trình."],
        ];
    }

    public function updateGoal(Request $request)
    {
        $data = $request->validate([
            'target_band' => ['nullable', 'string', 'max:10'],
            'study_minutes_per_day' => ['required', 'integer', 'min:10', 'max:180'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Đã cập nhật mục tiêu học tập.');
    }
}
