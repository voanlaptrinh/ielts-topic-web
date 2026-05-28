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
        $learningPlan = $this->learningPlan($recentAttempts);
        $benchmarkWins = $this->benchmarkWins();
        $skillTracks = $this->skillTracks();
        $marketEdges = $this->marketEdges();
        $qualitySignals = $this->qualitySignals();
        $growthRoadmap = $this->growthRoadmap();
        $faqs = $this->faqs();
        $structuredData = $this->structuredData($faqs);

        return view('topics.index', compact('topics', 'stats', 'recentAttempts', 'learningPlan', 'benchmarkWins', 'skillTracks', 'marketEdges', 'qualitySignals', 'growthRoadmap', 'faqs', 'structuredData'));
    }

    public function show(string $slug)
    {
        $topic = Topic::where('slug', $slug)->firstOrFail();

        return view('topics.show', compact('topic'));
    }

    private function learningPlan($recentAttempts): array
    {
        $attempts = auth()->check()
            ? TestAttempt::where('user_id', auth()->id())->latest()->take(12)->get()
            : collect();
        $totalQuestions = $attempts->sum('total');
        $accuracy = $totalQuestions > 0 ? round(($attempts->sum('score') / $totalQuestions) * 100) : null;
        $weakItems = $attempts
            ->flatMap(fn (TestAttempt $attempt) => collect($attempt->details ?? [])
                ->reject(fn ($detail) => $detail['is_correct'] ?? false)
                ->map(fn ($detail) => [
                    'prompt' => $detail['word'] ?? 'Câu hỏi cần ôn',
                    'correct' => $detail['correct'] ?? null,
                    'test_type' => $attempt->test_type,
                ]))
            ->take(3)
            ->values();
        $recommendedLevel = $accuracy === null || $accuracy < 55
            ? 'pre-intermediate'
            : ($accuracy < 75 ? 'intermediate' : 'upper-intermediate');

        return [
            'accuracy' => $accuracy,
            'attempts' => $attempts->count(),
            'weak_items' => $weakItems,
            'recommended_level' => $recommendedLevel,
            'recommended_route' => route('tests.level', $recommendedLevel),
            'today' => [
                [
                    'time' => '8 phút',
                    'title' => 'Khởi động bằng 1 topic IELTS',
                    'description' => 'Chọn một chủ đề, đọc câu hỏi và tự nói nháp trước khi xem bài mẫu.',
                    'route' => route('topics.index') . '#topic-bank',
                ],
                [
                    'time' => '12 phút',
                    'title' => 'Làm bài theo cấp độ phù hợp',
                    'description' => 'Bài ngắn có chấm điểm và giải thích để biết ngay điểm yếu.',
                    'route' => route('tests.level', $recommendedLevel),
                ],
                [
                    'time' => '5 phút',
                    'title' => 'Ôn lại lỗi sai hoặc flashcard',
                    'description' => 'Ưu tiên lỗi vừa sai; nếu chưa có lịch sử thì dùng flashcard để tạo vốn từ.',
                    'route' => $weakItems->isNotEmpty() && auth()->check()
                        ? route('history.index')
                        : route('vocabularies.flashcards'),
                ],
            ],
        ];
    }

    private function benchmarkWins(): array
    {
        return [
            [
                'source' => 'British Council',
                'strength' => 'lộ trình tự học, bài tương tác và theo dõi tiến độ',
                'our_move' => 'gom topic IELTS, từ vựng, test và lịch sử lỗi trong cùng một dashboard tiếng Việt.',
            ],
            [
                'source' => 'Cambridge English',
                'strength' => 'hoạt động theo CEFR và luyện nhiều kỹ năng',
                'our_move' => 'chia 6 mức IELTS, kèm dạng bài Reading, word family, collocation và paraphrase.',
            ],
            [
                'source' => 'Duolingo',
                'strength' => 'bài ngắn, game hóa và ôn tập lặp lại',
                'our_move' => 'đưa ra kế hoạch 25 phút/ngày, nhắc ôn lỗi sai thật thay vì chỉ luyện ngẫu nhiên.',
            ],
        ];
    }

    private function skillTracks(): array
    {
        return [
            ['label' => 'Speaking/Writing topics', 'value' => 'Ý tưởng, bài mẫu, mẹo band điểm'],
            ['label' => 'Vocabulary engine', 'value' => 'Từ học thuật, ví dụ, flashcard, quiz'],
            ['label' => 'IELTS reading skills', 'value' => 'T/F/NG, headings, summary, reference words'],
            ['label' => 'Error review', 'value' => 'Tự gom lỗi sai và đáp án đúng sau mỗi bài'],
        ];
    }

    private function marketEdges(): array
    {
        return [
            [
                'title' => 'Thiết kế cho người học Việt Nam',
                'description' => 'Giao diện tiếng Việt, giải thích ngắn gọn, ưu tiên lỗi thường gặp khi tự học IELTS tại nhà.',
            ],
            [
                'title' => 'Học theo hành động tiếp theo',
                'description' => 'Mỗi lượt vào trang đều có kế hoạch 25 phút, bài phù hợp cấp độ và mục cần ôn lại.',
            ],
            [
                'title' => 'Một nơi cho cả topic và từ vựng',
                'description' => 'Không tách rời Speaking, Writing, từ điển, flashcard, quiz và lịch sử luyện tập.',
            ],
        ];
    }

    private function qualitySignals(): array
    {
        return [
            'Lộ trình 6 cấp độ',
            'Theo dõi lỗi sai thật',
            'Từ điển Anh - Việt theo ngữ cảnh',
            'Bài luyện ngắn, dễ duy trì',
        ];
    }

    private function growthRoadmap(): array
    {
        return [
            [
                'stage' => 'Nền tảng học',
                'title' => 'Topic, từ vựng và bài test phải kết nối với nhau',
                'description' => 'Người học không chỉ đọc tài liệu mà còn có đường đi tiếp theo: tra từ, luyện bài, xem lỗi sai và quay lại ôn.',
            ],
            [
                'stage' => 'Nền tảng SEO',
                'title' => 'Mỗi trang cần trả lời đúng một nhu cầu tìm kiếm',
                'description' => 'Trang topic phục vụ câu hỏi Speaking/Writing, trang từ vựng phục vụ tra nghĩa, trang test phục vụ luyện kỹ năng theo cấp độ.',
            ],
            [
                'stage' => 'Nền tảng giữ chân',
                'title' => 'Top 1 cần người dùng quay lại',
                'description' => 'Lịch sử lỗi sai, kế hoạch 25 phút và gợi ý cấp độ giúp web có lý do để người học dùng hằng ngày.',
            ],
        ];
    }

    private function faqs(): array
    {
        return [
            [
                'question' => 'IELTS Focus phù hợp với ai?',
                'answer' => 'IELTS Focus phù hợp với người học Việt Nam muốn tự học IELTS theo lộ trình rõ ràng, có topic, từ vựng, từ điển, bài luyện và lịch sử lỗi sai.',
            ],
            [
                'question' => 'Web có thay thế giáo viên IELTS không?',
                'answer' => 'Web giúp bạn tự luyện đều hơn và biết lỗi cần ôn, nhưng phản hồi chuyên sâu cho Speaking/Writing vẫn nên kết hợp giáo viên hoặc người chấm có kinh nghiệm.',
            ],
            [
                'question' => 'Nên học IELTS Focus mỗi ngày như thế nào?',
                'answer' => 'Bạn có thể bắt đầu với kế hoạch 25 phút: 8 phút topic, 12 phút bài test theo cấp độ và 5 phút ôn lỗi sai hoặc flashcard.',
            ],
            [
                'question' => 'Làm sao để web này cạnh tranh top đầu?',
                'answer' => 'Cần tiếp tục tăng chất lượng nội dung, thêm nhiều topic thật, tối ưu tốc độ, xây dựng backlink uy tín và theo dõi dữ liệu tìm kiếm sau khi deploy.',
            ],
        ];
    }

    private function structuredData(array $faqs): array
    {
        return [
            'website' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => 'IELTS Focus',
                'url' => route('topics.index'),
                'inLanguage' => 'vi-VN',
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => route('vocabularies.index') . '?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ],
            'organization' => [
                '@context' => 'https://schema.org',
                '@type' => 'EducationalOrganization',
                'name' => 'IELTS Focus',
                'url' => route('topics.index'),
                'description' => 'Nền tảng tự học IELTS bằng tiếng Việt với topic, từ vựng, từ điển, bài test và lịch sử lỗi sai.',
                'areaServed' => 'VN',
                'knowsAbout' => ['IELTS', 'IELTS Speaking', 'IELTS Writing', 'IELTS Vocabulary', 'IELTS Reading'],
            ],
            'faq' => [
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => collect($faqs)->map(fn (array $faq) => [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ],
                ])->values()->all(),
            ],
        ];
    }
}
