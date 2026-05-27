<?php

namespace Tests\Feature;

use App\Models\DictionaryEntry;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_lookup_and_test_attempt_are_saved(): void
    {
        $user = User::factory()->create();

        DictionaryEntry::create([
            'word' => 'run',
            'normalized_word' => 'run',
            'part_of_speech' => 'verb',
            'definition' => 'to move fast by using your feet',
            'definition_vi' => '(động từ) chạy',
            'source_id' => 'history-run-v',
        ]);

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

        $this->actingAs($user)->get('/dictionary/run')->assertOk();

        $this->actingAs($user)->post('/tests/levels/intermediate/vocabulary', [
            'answers' => [$word->id => 'sai'],
        ])->assertOk();

        $this->assertDatabaseHas('word_lookup_histories', [
            'user_id' => $user->id,
            'normalized_word' => 'run',
        ]);

        $this->assertDatabaseHas('test_attempts', [
            'user_id' => $user->id,
            'test_type' => 'Từ vựng',
            'score' => 0,
            'total' => 1,
        ]);

        $this->actingAs($user)
            ->get('/history')
            ->assertOk()
            ->assertSee('Lịch sử học tập')
            ->assertSee('run')
            ->assertSee('Từ vựng');
    }
}
