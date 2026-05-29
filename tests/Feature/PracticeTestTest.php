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
        $word = $this->createVocabulary('sustainable', 'bền vững');

        $this->post('/tests/levels/intermediate/vocabulary', [
            'answers' => [$word->id => 'sai nghĩa'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 0 / 1')
            ->assertSee('Nghĩa đúng là: bền vững');
    }

    public function test_grammar_submission_is_scored_with_explanation(): void
    {
        $entry = $this->createDictionaryEntry('allocate', 'verb');

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
            ->assertSee('Luyện Reading theo dạng bài')
            ->assertSee('Luyện Listening theo kỹ năng')
            ->assertSee('Cấp 1 - Foundation')
            ->assertSee('Cấp 6 - Advanced');

        $this->get('/tests/levels/foundation')
            ->assertOk()
            ->assertSee('Cấp 1 - Foundation')
            ->assertSee('Chọn nghĩa đúng');

        $this->get('/tests/reading')
            ->assertOk()
            ->assertSee('IELTS Reading')
            ->assertSee('True / False / Not Given');

        $this->get('/tests/listening')
            ->assertOk()
            ->assertSee('IELTS Listening')
            ->assertSee('Form Completion');
    }

    public function test_extended_practice_pages_are_available(): void
    {
        $this->seedPracticeData();

        $this->get('/tests/levels/intermediate/definition')
            ->assertOk()
            ->assertSee('Bài test chọn từ theo định nghĩa');

        $this->get('/tests/levels/intermediate/spelling')
            ->assertOk()
            ->assertSee('Bài test chính tả');

        $this->get('/tests/levels/intermediate/example-completion')
            ->assertOk()
            ->assertSee('Bài test điền từ vào câu');

        $this->get('/tests/levels/intermediate/ielts-format')
            ->assertOk()
            ->assertSee('Bài tổng hợp theo dạng IELTS');

        $this->get('/tests/levels/intermediate/skills/true-false-not-given')
            ->assertOk()
            ->assertSee('True / False / Not Given');
    }

    public function test_extended_practice_submissions_are_scored(): void
    {
        $entry = $this->createDictionaryEntry('allocate', 'verb');
        $word = $this->createVocabulary('sustainable', 'bền vững');

        $this->post('/tests/levels/intermediate/definition', [
            'answers' => [$entry->id => 'allocate'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 1');

        $this->post('/tests/levels/intermediate/spelling', [
            'answers' => [$word->id => 'sustainable'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 1');

        $this->post('/tests/levels/intermediate/example-completion', [
            'answers' => [$word->id => 'sustainable'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 1');

        $this->post('/tests/levels/intermediate/ielts-format', [
            'answers' => [0 => 'Waste reduction should happen before recycling becomes necessary.'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 1');

        $this->post('/tests/levels/intermediate/skills/true-false-not-given', [
            'answers' => [0 => 'True'],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 1');
    }

    public function test_practice_submissions_require_answers(): void
    {
        $this->from('/tests/levels/intermediate')
            ->post('/tests/levels/intermediate/vocabulary', [])
            ->assertRedirect('/tests/levels/intermediate')
            ->assertSessionHasErrors('answers');
    }

    private function seedPracticeData(): void
    {
        $this->createDictionaryEntry('allocate', 'verb');
        $this->createDictionaryEntry('policy', 'noun');
        $this->createDictionaryEntry('specific', 'adjective');
        $this->createDictionaryEntry('rapidly', 'adverb');

        $this->createVocabulary('sustainable', 'bền vững');
        $this->createVocabulary('transport', 'giao thông');
        $this->createVocabulary('evidence', 'bằng chứng');
        $this->createVocabulary('beneficial', 'có lợi');
    }

    private function createDictionaryEntry(string $word, string $partOfSpeech): DictionaryEntry
    {
        return DictionaryEntry::create([
            'word' => $word,
            'normalized_word' => $word,
            'part_of_speech' => $partOfSpeech,
            'definition' => "definition for {$word}",
            'definition_vi' => "nghĩa tiếng Việt của {$word}",
            'source_id' => "test-{$word}-{$partOfSpeech}",
        ]);
    }

    private function createVocabulary(string $word, string $meaning): Vocabulary
    {
        return Vocabulary::create([
            'word' => $word,
            'phonetic' => '/test/',
            'part_of_speech' => 'adjective',
            'meaning_vi' => $meaning,
            'definition_en' => "definition for {$word}",
            'example_en' => ucfirst($word) . ' transport is important.',
            'example_vi' => 'Ví dụ tiếng Việt.',
            'topic' => 'Environment',
            'level' => 'B2',
        ]);
    }
}
