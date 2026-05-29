<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
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

        foreach ($faqs as $index => $faq) {
            Faq::updateOrCreate(
                ['question' => $faq['question']],
                [
                    'answer' => $faq['answer'],
                    'position' => $index + 1,
                    'is_published' => true,
                ]
            );
        }
    }
}
