<?php

namespace Database\Seeders;

use App\Models\DictionaryEntry;
use App\Models\Vocabulary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DictionaryEntrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $directory = database_path('seeders/data/oewn2025');
        $files = [
            'noun' => ['file' => 'data.noun', 'pos' => 'noun'],
            'verb' => ['file' => 'data.verb', 'pos' => 'verb'],
            'adj' => ['file' => 'data.adj', 'pos' => 'adjective'],
            'adv' => ['file' => 'data.adv', 'pos' => 'adverb'],
        ];

        $now = now();
        $records = [];
        $vietnameseMeanings = Vocabulary::pluck('meaning_vi', 'word')
            ->mapWithKeys(fn ($meaning, $word) => [strtolower($word) => $meaning]);

        foreach ($files as $meta) {
            $path = $directory . DIRECTORY_SEPARATOR . $meta['file'];

            if (! file_exists($path)) {
                continue;
            }

            $handle = fopen($path, 'r');

            if ($handle === false) {
                continue;
            }

            while (($line = fgets($handle)) !== false) {
                $entries = $this->parseLine($line, $meta['pos'], $now, $vietnameseMeanings);

                if ($entries === []) {
                    continue;
                }

                foreach ($entries as $entry) {
                    $records[] = $entry;

                    if (count($records) >= 1000) {
                        $this->upsert($records);
                        $records = [];
                    }
                }
            }

            fclose($handle);
        }

        if ($records !== []) {
            $this->upsert($records);
        }
    }

    private function parseLine(string $line, string $partOfSpeech, $timestamp, $vietnameseMeanings): array
    {
        $line = trim($line);

        if ($line === '' || ! ctype_digit($line[0])) {
            return [];
        }

        [$data, $gloss] = array_pad(explode('|', $line, 2), 2, '');
        $tokens = preg_split('/\s+/', trim($data));

        if (! is_array($tokens) || count($tokens) < 5) {
            return [];
        }

        $offset = $tokens[0];
        $wordCount = hexdec($tokens[3]);
        $words = [];
        $cursor = 4;

        for ($i = 0; $i < $wordCount; $i++) {
            if (! isset($tokens[$cursor])) {
                break;
            }

            $words[] = str_replace('_', ' ', $tokens[$cursor]);
            $cursor += 2;
        }

        if ($words === []) {
            return [];
        }

        $glossParts = array_map('trim', explode(';', trim($gloss)));
        $definition = array_shift($glossParts);
        $examples = collect($glossParts)
            ->map(fn ($example) => trim($example, " \t\n\r\0\x0B\""))
            ->filter()
            ->values()
            ->all();

        if ($definition === null || $definition === '') {
            return [];
        }

        $records = [];

        foreach ($words as $word) {
            $synonyms = array_values(array_filter($words, fn ($candidate) => $candidate !== $word));

            $records[] = [
                'word' => $word,
                'normalized_word' => strtolower($word),
                'part_of_speech' => $partOfSpeech,
                'definition' => $definition,
                'definition_vi' => $this->makeVietnameseContext($word, $partOfSpeech, $definition, $vietnameseMeanings),
                'examples' => $examples === [] ? null : json_encode($examples),
                'synonyms' => $synonyms === [] ? null : json_encode($synonyms),
                'source' => 'Open English WordNet 2025',
                'source_id' => $offset . '-' . $partOfSpeech . '-' . strtolower($word),
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }

        return $records;
    }

    private function upsert(array $records): void
    {
        DB::table((new DictionaryEntry())->getTable())->upsert(
            $records,
            ['source_id'],
            [
                'word',
                'normalized_word',
                'part_of_speech',
                'definition',
                'definition_vi',
                'examples',
                'synonyms',
                'source',
                'updated_at',
            ]
        );
    }

    private function makeVietnameseContext(string $word, string $partOfSpeech, string $definition, $vietnameseMeanings): string
    {
        $meaning = $vietnameseMeanings->get(strtolower($word));

        if ($meaning && $meaning !== 'Đang cập nhật nghĩa tiếng Việt.') {
            return $meaning;
        }

        $labels = [
            'noun' => 'danh từ',
            'verb' => 'động từ',
            'adjective' => 'tính từ',
            'adverb' => 'trạng từ',
        ];

        $label = $labels[$partOfSpeech] ?? 'từ';

        return "({$label}) Giải thích tiếng Anh: {$definition}";
    }
}
