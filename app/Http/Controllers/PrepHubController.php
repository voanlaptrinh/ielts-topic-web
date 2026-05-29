<?php

namespace App\Http\Controllers;

class PrepHubController extends Controller
{
    public function index()
    {
        return view('prep.index', [
            'skills' => $this->skills(),
            'plans' => $this->studyPlans(),
            'criteria' => $this->bandCriteria(),
            'mistakes' => $this->commonMistakes(),
            'mockChecklist' => $this->mockChecklist(),
        ]);
    }

    private function skills(): array
    {
        return [
            [
                'name' => 'Listening',
                'route' => route('tests.listening'),
                'focus' => 'Nghe thông tin chính, chi tiết, thái độ người nói và theo dõi thứ tự câu hỏi.',
                'tasks' => ['Form completion', 'Multiple choice', 'Map labelling', 'Matching'],
            ],
            [
                'name' => 'Reading',
                'route' => route('tests.reading'),
                'focus' => 'Đọc nhanh ý chính, định vị keyword, nhận diện paraphrase và kiểm soát thời gian.',
                'tasks' => ['True/False/Not Given', 'Matching headings', 'Summary completion', 'Matching information'],
            ],
            [
                'name' => 'Writing',
                'route' => route('tests.writing'),
                'focus' => 'Viết đúng yêu cầu đề, bố cục rõ, phát triển luận điểm và dùng ngôn ngữ chính xác.',
                'tasks' => ['Task 1 report/letter', 'Task 2 essay', 'Band sample comparison'],
            ],
            [
                'name' => 'Speaking',
                'route' => route('tests.speaking'),
                'focus' => 'Nói rõ ràng, mở rộng ý, dùng từ linh hoạt và giữ fluency trong cả 3 phần.',
                'tasks' => ['Part 1 personal topics', 'Part 2 cue card', 'Part 3 discussion'],
            ],
        ];
    }

    private function studyPlans(): array
    {
        return [
            ['target' => 'Band 5.0 - 5.5', 'plan' => 'Ôn nền grammar, từ vựng topic phổ biến, luyện short answer và đọc passage ngắn mỗi ngày.'],
            ['target' => 'Band 6.0 - 6.5', 'plan' => 'Làm đề timed 3-4 buổi/tuần, phân tích lỗi sai, viết Task 2 có ví dụ cụ thể và luyện Speaking Part 2 đủ 2 phút.'],
            ['target' => 'Band 7.0+', 'plan' => 'Tập trung paraphrase, logic bài viết, collocation học thuật, mock test đủ 4 kỹ năng và review feedback sau mỗi bài.'],
        ];
    }

    private function bandCriteria(): array
    {
        return [
            'Writing' => ['Task response/achievement', 'Coherence and cohesion', 'Lexical resource', 'Grammar range and accuracy'],
            'Speaking' => ['Fluency and coherence', 'Lexical resource', 'Grammar range and accuracy', 'Pronunciation'],
            'Reading/Listening' => ['Độ chính xác đáp án', 'Khả năng theo format đề', 'Quản lý thời gian', 'Review lỗi sai sau bài làm'],
        ];
    }

    private function commonMistakes(): array
    {
        return [
            'Listening' => 'Mất đáp án vì chờ đúng từ trong câu hỏi, không nghe paraphrase hoặc sai số ít/số nhiều.',
            'Reading' => 'Đọc toàn bài quá lâu, chọn theo keyword giống hệt mà bỏ qua ý phủ định hoặc Not Given.',
            'Writing' => 'Không trả lời đúng yêu cầu đề, thiếu ví dụ cụ thể, đoạn văn dài nhưng logic yếu.',
            'Speaking' => 'Trả lời quá ngắn, học thuộc máy móc, thiếu linking words và không mở rộng lý do.',
        ];
    }

    private function mockChecklist(): array
    {
        return [
            'Làm Listening một lần duy nhất, không tua lại audio.',
            'Reading giới hạn 60 phút và tự chia thời gian cho từng passage.',
            'Writing Task 1 khoảng 20 phút, Task 2 khoảng 40 phút.',
            'Speaking luyện đủ Part 1, Part 2, Part 3 và ghi lại câu trả lời để review.',
            'Sau mock test, lưu lỗi sai vào dashboard và học lại trước khi làm đề mới.',
        ];
    }
}
