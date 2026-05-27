<?php

namespace Database\Seeders;

use App\Models\Vocabulary;
use Illuminate\Database\Seeder;

class VocabularySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $words = [
            [
                'word' => 'sustainable',
                'phonetic' => '/səˈsteɪnəbl/',
                'part_of_speech' => 'adjective',
                'meaning_vi' => 'bền vững, có thể duy trì lâu dài',
                'definition_en' => 'Able to continue over a long period without harming the environment or exhausting resources.',
                'example_en' => 'Governments should encourage sustainable transport in large cities.',
                'example_vi' => 'Chính phủ nên khuyến khích giao thông bền vững ở các thành phố lớn.',
                'topic' => 'Environment',
                'level' => 'B2',
                'synonyms' => ['eco-friendly', 'long-term', 'viable'],
            ],
            [
                'word' => 'allocate',
                'phonetic' => '/ˈæləkeɪt/',
                'part_of_speech' => 'verb',
                'meaning_vi' => 'phân bổ, cấp phát',
                'definition_en' => 'To give something officially to someone or something for a particular purpose.',
                'example_en' => 'Schools need to allocate more time to practical communication skills.',
                'example_vi' => 'Trường học cần phân bổ thêm thời gian cho kỹ năng giao tiếp thực tế.',
                'topic' => 'Education',
                'level' => 'B2',
                'synonyms' => ['assign', 'distribute', 'apportion'],
            ],
            [
                'word' => 'consequence',
                'phonetic' => '/ˈkɑːnsəkwens/',
                'part_of_speech' => 'noun',
                'meaning_vi' => 'hậu quả, kết quả',
                'definition_en' => 'A result or effect of an action or condition.',
                'example_en' => 'One consequence of heavy traffic is a decline in air quality.',
                'example_vi' => 'Một hậu quả của giao thông đông đúc là chất lượng không khí giảm.',
                'topic' => 'Society',
                'level' => 'B1',
                'synonyms' => ['result', 'effect', 'outcome'],
            ],
            [
                'word' => 'beneficial',
                'phonetic' => '/ˌbenɪˈfɪʃl/',
                'part_of_speech' => 'adjective',
                'meaning_vi' => 'có lợi, hữu ích',
                'definition_en' => 'Having a helpful or positive effect.',
                'example_en' => 'Regular reading is beneficial for vocabulary development.',
                'example_vi' => 'Đọc sách thường xuyên có lợi cho việc phát triển từ vựng.',
                'topic' => 'Education',
                'level' => 'B1',
                'synonyms' => ['helpful', 'useful', 'advantageous'],
            ],
            [
                'word' => 'urbanization',
                'phonetic' => '/ˌɜːrbənəˈzeɪʃn/',
                'part_of_speech' => 'noun',
                'meaning_vi' => 'đô thị hóa',
                'definition_en' => 'The process by which towns and cities grow as more people live and work in them.',
                'example_en' => 'Rapid urbanization can put pressure on housing and public transport.',
                'example_vi' => 'Đô thị hóa nhanh có thể tạo áp lực lên nhà ở và giao thông công cộng.',
                'topic' => 'Cities',
                'level' => 'C1',
                'synonyms' => ['city growth', 'urban growth'],
            ],
            [
                'word' => 'adapt',
                'phonetic' => '/əˈdæpt/',
                'part_of_speech' => 'verb',
                'meaning_vi' => 'thích nghi, điều chỉnh',
                'definition_en' => 'To change your behaviour or ideas to deal successfully with a new situation.',
                'example_en' => 'Students must adapt to different learning environments.',
                'example_vi' => 'Học sinh phải thích nghi với các môi trường học tập khác nhau.',
                'topic' => 'Work and Study',
                'level' => 'B1',
                'synonyms' => ['adjust', 'modify', 'change'],
            ],
        ];

        foreach ($words as $word) {
            Vocabulary::updateOrCreate(
                ['word' => $word['word']],
                $word
            );
        }

        $this->importNawl();
    }

    private function importNawl(): void
    {
        $path = database_path('seeders/data/nawl.csv');

        if (! file_exists($path)) {
            return;
        }

        $handle = fopen($path, 'r');

        if ($handle === false) {
            return;
        }

        $existingWords = Vocabulary::all()->keyBy('word');
        $records = [];

        fgetcsv($handle);

        while (($row = fgetcsv($handle)) !== false) {
            [$word, $definition, $partOfSpeech] = array_pad($row, 3, null);

            $word = trim((string) $word);
            $definition = trim((string) $definition);
            $partOfSpeech = trim((string) $partOfSpeech);

            if ($word === '' || $definition === '') {
                continue;
            }

            $existing = $existingWords->get($word);

            $records[] = [
                'word' => $word,
                'phonetic' => $existing?->phonetic,
                'part_of_speech' => $existing?->part_of_speech ?: ($partOfSpeech ?: 'word'),
                'meaning_vi' => $existing?->meaning_vi ?: 'Đang cập nhật nghĩa tiếng Việt.',
                'definition_en' => $existing?->definition_en ?: $definition,
                'example_en' => $existing?->example_en ?: "The word \"{$word}\" is useful in academic IELTS contexts.",
                'example_vi' => $existing?->example_vi ?: 'Đang cập nhật ví dụ tiếng Việt.',
                'topic' => $existing?->topic ?: 'Academic Vocabulary',
                'level' => $existing?->level ?: 'IELTS',
                'synonyms' => $existing?->synonyms ? json_encode($existing->synonyms) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        foreach (array_chunk($records, 250) as $chunk) {
            Vocabulary::upsert(
                $chunk,
                ['word'],
                [
                    'phonetic',
                    'part_of_speech',
                    'meaning_vi',
                    'definition_en',
                    'example_en',
                    'example_vi',
                    'topic',
                    'level',
                    'synonyms',
                    'updated_at',
                ]
            );
        }
    }
}
