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
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        $topics = Topic::orderBy('part')->orderBy('title')->get();
        $stats = [
            'topics' => max(450, Topic::count()),
            'vocabularies' => max(15000, Vocabulary::count()),
            'dictionary_words' => max(25000, DictionaryEntry::distinct('normalized_word')->count('normalized_word')),
        ];
        $recentAttempts = auth()->check()
            ? TestAttempt::where('user_id', auth()->id())->latest()->take(3)->get()
            : collect();

        $learningPlan = $this->learningPlan($recentAttempts);
        $marketEdges = $this->marketEdges();
        $officialPrepSummary = $this->officialPrepSummary();
        $strategySteps = $this->strategySteps();
        $courseTracks = $this->courseTracks();
        $testLibrary = $this->testLibrary();
        $topicCards = $this->topicCards($topics);
        $faqs = $this->faqs();
        $structuredData = $this->structuredData($faqs);

        return view('topics.index', compact(
            'topics',
            'stats',
            'recentAttempts',
            'learningPlan',
            'marketEdges',
            'officialPrepSummary',
            'strategySteps',
            'courseTracks',
            'testLibrary',
            'topicCards',
            'faqs',
            'structuredData'
        ));
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

        return [
            'accuracy' => $accuracy,
            'attempts' => $attempts->count(),
            'weak_items' => $weakItems,
            'recommended_route' => route('tests.index'),
            'today' => [
                [
                    'number' => 1,
                    'title' => 'Khởi động bằng một topic IELTS',
                    'description' => 'Chọn 1 chủ đề và ôn tập kiến thức liên quan',
                    'route' => route('topics.index') . '#topic-bank',
                ],
                [
                    'number' => 2,
                    'title' => 'Làm bài theo cấp độ phù hợp',
                    'description' => 'Luyện tập với bài phù hợp trình độ của bạn',
                    'route' => route('tests.index'),
                ],
                [
                    'number' => 3,
                    'title' => 'Ôn lại lỗi sai hoặc thẻ ôn từ',
                    'description' => 'Củng cố kiến thức và tránh lặp lại lỗi sai',
                    'route' => $weakItems->isNotEmpty() && auth()->check()
                        ? route('history.index')
                        : route('vocabularies.flashcards'),
                ],
            ],
        ];
    }

    private function marketEdges(): array
    {
        return [
            [
                'icon' => 'flag',
                'title' => 'Thiết kế cho người học Việt Nam',
                'description' => 'Nội dung bám sát nhu cầu và khó khăn của người học Việt.',
            ],
            [
                'icon' => 'puzzle',
                'title' => 'Tích hợp toàn diện',
                'description' => 'Kết hợp topic, từ vựng, từ điển, bài luyện và lịch sử lỗi sai.',
            ],
            [
                'icon' => 'tasks',
                'title' => 'Học theo hành động tiếp theo',
                'description' => 'Gợi ý rõ ràng mỗi ngày giúp bạn tiến bộ đều đặn.',
            ],
            [
                'icon' => 'route',
                'title' => 'Theo dõi và cá nhân hóa',
                'description' => 'Theo dõi tiến độ và đưa ra gợi ý phù hợp với bạn.',
            ],
        ];
    }

    private function officialPrepSummary(): array
    {
        return [
            'Luyện đủ 4 kỹ năng Nghe - Nói - Đọc - Viết',
            'Làm bài có thời gian như thi thật',
            'Xem đáp án và giải thích chi tiết',
            'Theo dõi tiến độ học tập',
            'Lưu và ôn lại lỗi sai',
            'Có lộ trình học rõ ràng',
        ];
    }

    private function strategySteps(): array
    {
        return [
            ['icon' => 'list', 'label' => 'Chọn topic học'],
            ['icon' => 'tasks', 'label' => 'Làm bài luyện tập'],
            ['icon' => 'edit', 'label' => 'Xem lỗi sai chi tiết'],
            ['icon' => 'refresh', 'label' => 'Ôn lại kiến thức'],
            ['icon' => 'chart', 'label' => 'Theo dõi tiến độ'],
        ];
    }

    private function courseTracks(): array
    {
        return [
            [
                'icon' => 'book',
                'title' => 'IELTS nền tảng',
                'level' => 'Band 0 - 4.5',
                'description' => 'Củng cố ngữ pháp, từ vựng học thuật và thói quen làm bài có thời gian.',
                'focus' => ['Từ vựng cốt lõi', 'Ngữ pháp nền', 'Bài tập ngắn'],
                'route' => route('tests.level', 'foundation'),
            ],
            [
                'icon' => 'tasks',
                'title' => 'Listening & Reading Lab',
                'level' => 'Band 4.5 - 6.5',
                'description' => 'Luyện kỹ năng bắt ý, quét thông tin, quản lý thời gian và kiểm tra đáp án.',
                'focus' => ['Listening 4 phần', 'Reading 3 bài đọc', 'Giải thích lỗi sai'],
                'route' => route('tests.index'),
            ],
            [
                'icon' => 'edit',
                'title' => 'Writing & Speaking Coach',
                'level' => 'Band 5.0 - 7.0+',
                'description' => 'Thực hành trả lời theo tiêu chí IELTS, lưu bài làm và xem phản hồi sau khi chấm.',
                'focus' => ['Task response', 'Fluency', 'Lexical resource'],
                'route' => route('tests.writing'),
            ],
        ];
    }

    private function testLibrary(): array
    {
        return [
            [
                'skill' => 'Listening',
                'time' => '40 phút',
                'parts' => '4 phần',
                'questions' => '40 câu',
                'description' => 'Bài nghe mô phỏng theo kỹ năng nghe hiểu, ghi chú nhanh và chọn đáp án.',
                'route' => route('tests.listening'),
            ],
            [
                'skill' => 'Reading',
                'time' => '60 phút',
                'parts' => '3 bài đọc',
                'questions' => '40 câu',
                'description' => 'Bài đọc học thuật với mục tiêu tìm ý chính, chi tiết và suy luận.',
                'route' => route('tests.reading'),
            ],
            [
                'skill' => 'Writing',
                'time' => '60 phút',
                'parts' => '2 task',
                'questions' => 'Chấm sau',
                'description' => 'Không gian viết bài, lưu lịch sử và nhận phản hồi theo tiêu chí IELTS.',
                'route' => route('tests.writing'),
            ],
            [
                'skill' => 'Speaking',
                'time' => '11 - 14 phút',
                'parts' => '3 phần',
                'questions' => 'Gợi ý trả lời',
                'description' => 'Luyện nói theo cue card, ghi lại ý tưởng và theo dõi chủ đề đã luyện.',
                'route' => route('tests.speaking'),
            ],
        ];
    }

    private function topicCards($topics): array
    {
        $fallback = [
            ['title' => 'Education', 'slug' => null, 'description' => 'Các câu hỏi và bài mẫu về giáo dục.', 'difficulty' => 'Trung bình', 'image' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?auto=format&fit=crop&w=520&q=80'],
            ['title' => 'Environment', 'slug' => null, 'description' => 'Chủ đề về môi trường và bảo vệ hành tinh.', 'difficulty' => 'Khó', 'image' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?auto=format&fit=crop&w=520&q=80'],
            ['title' => 'Technology', 'slug' => null, 'description' => 'Ảnh hưởng của công nghệ đến cuộc sống.', 'difficulty' => 'Trung bình', 'image' => 'https://images.unsplash.com/photo-1593508512255-86ab42a8e620?auto=format&fit=crop&w=520&q=80'],
            ['title' => 'Health', 'slug' => null, 'description' => 'Các vấn đề về sức khỏe và lối sống lành mạnh.', 'difficulty' => 'Dễ', 'image' => 'https://images.unsplash.com/photo-1506126613408-eca07ce68773?auto=format&fit=crop&w=520&q=80'],
            ['title' => 'Culture', 'slug' => null, 'description' => 'Văn hóa, truyền thống và xã hội.', 'difficulty' => 'Trung bình', 'image' => 'https://images.unsplash.com/photo-1514933651103-005eec06c04b?auto=format&fit=crop&w=520&q=80'],
        ];

        return collect($fallback)->map(function (array $card) use ($topics) {
            $topic = $topics->first(fn (Topic $item) => str_contains(strtolower($item->title), strtolower($card['title'])));

            return [
                ...$card,
                'slug' => $topic?->slug,
                'description' => $topic?->description ?: $card['description'],
                'difficulty' => $topic?->difficulty ?: $card['difficulty'],
            ];
        })->all();
    }

    private function faqs(): array
    {
        return [
            [
                'question' => 'IELTS Focus phù hợp với ai?',
                'answer' => 'IELTS Focus phù hợp với người học Việt Nam muốn tự học IELTS theo lộ trình rõ ràng, có topic, từ vựng, từ điển, bài luyện và lịch sử lỗi sai.',
            ],
            [
                'question' => 'Nên học mỗi ngày như thế nào?',
                'answer' => 'Bạn có thể bắt đầu với kế hoạch ngắn: chọn topic, làm bài luyện, sau đó ôn lại lỗi sai hoặc thẻ ôn từ.',
            ],
            [
                'question' => 'Web có thay thế giáo viên IELTS không?',
                'answer' => 'Web giúp bạn tự luyện đều hơn và biết lỗi cần ôn, nhưng phản hồi chuyên sâu cho Speaking/Writing vẫn nên kết hợp giáo viên hoặc người chấm có kinh nghiệm.',
            ],
            [
                'question' => 'Làm sao để học hiệu quả hơn?',
                'answer' => 'Hãy học đều mỗi ngày, làm bài có thời gian, xem giải thích lỗi sai và theo dõi tiến độ trong dashboard khi đã đăng nhập.',
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
                    'target' => route('search.index') . '?q={search_term_string}',
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
