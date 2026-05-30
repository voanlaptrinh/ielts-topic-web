<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vocabulary;
use Database\Seeders\VocabularySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VocabularyTest extends TestCase
{
    use RefreshDatabase;

    public function test_vocabulary_pages_are_available(): void
    {
        $this->seed(VocabularySeeder::class);

        $this->get('/vocabulary')->assertOk()->assertSee('Tra từ vựng IELTS nhanh');
        $this->get('/vocabulary/flashcards')->assertOk()->assertSee('Ôn từ bằng flashcard');
        $this->get('/vocabulary/quiz')->assertOk()->assertSee('Làm quiz từ vựng IELTS');
        $this->get('/vocabulary/sustainable')->assertOk()->assertSee('bền vững');
    }

    public function test_users_can_search_vocabulary(): void
    {
        $this->seed(VocabularySeeder::class);

        $this->get('/vocabulary?q=education')
            ->assertOk()
            ->assertSee('allocate')
            ->assertSee('beneficial');
    }

    public function test_users_can_review_vocabulary_by_topic(): void
    {
        $this->seed(VocabularySeeder::class);

        $this->get('/vocabulary?topic=Education')
            ->assertOk()
            ->assertSee('Ôn theo lĩnh vực')
            ->assertSee('Ôn từ theo topic')
            ->assertSee('Nhập nghĩa tiếng Việt')
            ->assertSee('Kiểm tra')
            ->assertSee('Education')
            ->assertSee('allocate')
            ->assertSee('beneficial');
    }

    public function test_vocabulary_live_search_returns_results_partial(): void
    {
        $this->seed(VocabularySeeder::class);

        $this->get('/vocabulary/search?q=sustainable', [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('Nghĩa tiếng Việt')
            ->assertSee('sustainable')
            ->assertDontSee('<html', false);
    }

    public function test_flashcards_lazy_load_returns_partial(): void
    {
        $this->seed(VocabularySeeder::class);

        $this->get('/vocabulary/flashcards?page=2', [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('flashcard')
            ->assertDontSee('<html', false);
    }

    public function test_vocabulary_quiz_submission_is_scored_and_shows_wrong_answers(): void
    {
        [$sustainable, $allocate] = $this->createQuizWords();

        $this->post('/vocabulary/quiz', [
            'answers' => [
                $sustainable->id => 'sai nghĩa',
                $allocate->id => 'phân bổ',
            ],
        ])
            ->assertOk()
            ->assertSee('Điểm: 1 / 2')
            ->assertSee('Các lỗi cần xem lại')
            ->assertSee('sustainable')
            ->assertSee('Bạn chọn:')
            ->assertSee('sai nghĩa')
            ->assertSee('Đáp án đúng:')
            ->assertSee('bền vững');
    }

    public function test_authenticated_vocabulary_quiz_attempt_is_saved_to_history(): void
    {
        $user = User::factory()->create();
        [$sustainable, $allocate] = $this->createQuizWords();

        $this->actingAs($user)->post('/vocabulary/quiz', [
            'answers' => [
                $sustainable->id => 'sai nghĩa',
                $allocate->id => 'phân bổ',
            ],
        ])->assertOk();

        $this->assertDatabaseHas('test_attempts', [
            'user_id' => $user->id,
            'test_type' => 'Quiz từ vựng',
            'level' => 'Ôn nhanh',
            'score' => 1,
            'total' => 2,
        ]);

        $this->actingAs($user)
            ->get('/history')
            ->assertOk()
            ->assertSee('Quiz từ vựng')
            ->assertSee('Lỗi sai cần xem lại')
            ->assertSee('sustainable')
            ->assertSee('sai nghĩa')
            ->assertSee('bền vững');
    }

    private function createQuizWords(): array
    {
        $sustainable = Vocabulary::create([
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

        $allocate = Vocabulary::create([
            'word' => 'allocate',
            'phonetic' => '/test/',
            'part_of_speech' => 'verb',
            'meaning_vi' => 'phân bổ',
            'definition_en' => 'to give something for a particular purpose',
            'example_en' => 'Schools allocate time for revision.',
            'example_vi' => 'Trường học phân bổ thời gian ôn tập.',
            'topic' => 'Education',
            'level' => 'B2',
        ]);

        Vocabulary::create([
            'word' => 'beneficial',
            'phonetic' => '/test/',
            'part_of_speech' => 'adjective',
            'meaning_vi' => 'có lợi',
            'definition_en' => 'helpful or positive',
            'example_en' => 'Reading is beneficial.',
            'example_vi' => 'Đọc sách có lợi.',
            'topic' => 'Education',
            'level' => 'B1',
        ]);

        Vocabulary::create([
            'word' => 'adapt',
            'phonetic' => '/test/',
            'part_of_speech' => 'verb',
            'meaning_vi' => 'thích nghi',
            'definition_en' => 'to change to fit a new situation',
            'example_en' => 'Students adapt quickly.',
            'example_vi' => 'Học sinh thích nghi nhanh.',
            'topic' => 'Study',
            'level' => 'B1',
        ]);

        return [$sustainable, $allocate];
    }
}
