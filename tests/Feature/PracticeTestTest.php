<?php

namespace Tests\Feature;

use App\Models\DictionaryEntry;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeTestTest extends TestCase
{
    use RefreshDatabase;

    public function test_vocabulary_submission_is_scored_with_explanation(): void
    {
        $word = Vocabulary::create([
            'word' => 'sustainable',
            'phonetic' => '/test/',
            'part_of_speech' => 'adjective',
            'meaning_vi' => 'bền vững',
            'definition_en' => 'able to continue for a long time',
            'example_en' => 'Sustainable transport is important.',
            'example_vi' => 'Giao thông bền vững rất quan trọng.',
            'topic' => 'Environment',
            'level' => 'B2',
        ]);

        $this->post('/tests/levels/intermediate/vocabulary', [
            'answers' => [$word->id => 'sai nghĩa'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 0 / 1')
            ->assertSee('Nghĩa đúng là: bền vững');
    }

    public function test_grammar_submission_is_scored_with_explanation(): void
    {
        $entry = DictionaryEntry::create([
            'word' => 'allocate',
            'normalized_word' => 'allocate',
            'part_of_speech' => 'verb',
            'definition' => 'to give something for a particular purpose',
            'definition_vi' => '(động từ) phân bổ',
            'source_id' => 'test-allocate-v',
        ]);

        $this->post('/tests/levels/intermediate/grammar', [
            'answers' => [$entry->id => 'noun'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 0 / 1')
            ->assertSee('Sai vì nghĩa này của');
    }

    public function test_level_pages_are_available(): void
    {
        $this->get('/tests')
            ->assertOk()
            ->assertSee('6 cấp độ luyện IELTS')
            ->assertSee('Cấp 1 - Foundation')
            ->assertSee('Cấp 6 - Advanced');

        $this->get('/tests/levels/foundation')
            ->assertOk()
            ->assertSee('Cấp 1 - Foundation')
            ->assertSee('Chọn nghĩa đúng');
    }
}
