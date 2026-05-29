<?php

namespace Tests\Feature;

use App\Models\Topic;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_requires_admin_user(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $this->actingAs(User::factory()->create(['is_admin' => false]))
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_admin_can_update_topic(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $topic = Topic::create([
            'title' => 'Old Topic',
            'slug' => 'old-topic',
            'description' => 'Old description',
            'part' => 'Part 1',
            'difficulty' => 'Beginner',
            'questions' => ['Old question?'],
            'sample_answer' => 'Old answer',
            'tips' => ['Old tip'],
        ]);

        $this->actingAs($admin)->put("/admin/topics/{$topic->id}", [
            'title' => 'New Topic',
            'slug' => 'new-topic',
            'description' => 'New description',
            'part' => 'Part 2',
            'difficulty' => 'Intermediate',
            'questions_text' => "Question one?\nQuestion two?",
            'sample_answer' => 'New answer',
            'tips_text' => 'Use examples.',
        ])->assertRedirect('/admin/topics');

        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'title' => 'New Topic',
            'slug' => 'new-topic',
        ]);
    }

    public function test_admin_can_update_vocabulary(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $word = Vocabulary::create([
            'word' => 'old',
            'phonetic' => '/old/',
            'part_of_speech' => 'adjective',
            'meaning_vi' => 'cũ',
            'definition_en' => 'not new',
            'example_en' => 'An old book.',
            'example_vi' => 'Một cuốn sách cũ.',
            'topic' => 'General',
            'level' => 'B1',
        ]);

        $this->actingAs($admin)->put("/admin/vocabularies/{$word->id}", [
            'word' => 'updated',
            'phonetic' => '/updated/',
            'part_of_speech' => 'verb',
            'meaning_vi' => 'cập nhật',
            'definition_en' => 'to make current',
            'example_en' => 'Students update notes.',
            'example_vi' => 'Học sinh cập nhật ghi chú.',
            'topic' => 'Study',
            'level' => 'B2',
            'synonyms_text' => 'revise, refresh',
        ])->assertRedirect('/admin/vocabularies');

        $this->assertDatabaseHas('vocabularies', [
            'id' => $word->id,
            'word' => 'updated',
            'meaning_vi' => 'cập nhật',
        ]);
    }
}
