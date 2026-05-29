<?php

namespace Tests\Feature;

use App\Models\PracticeTest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PracticeContentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_reading_practice_test_with_questions(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post('/admin/practice-tests', [
            'title' => 'Urban Parks Reading',
            'slug' => 'urban-parks-reading',
            'skill' => 'reading',
            'level' => 'intermediate',
            'duration_minutes' => 12,
            'description' => 'Practice true false not given.',
            'passage' => 'Urban parks can improve health.',
            'is_published' => '1',
            'questions' => [
                [
                    'prompt' => 'Parks can support public health.',
                    'question_type' => 'multiple_choice',
                    'options_text' => "True\nFalse\nNot Given",
                    'correct_answer' => 'True',
                    'explanation' => 'The passage says parks can improve health.',
                ],
            ],
        ])->assertRedirect('/admin/practice-tests');

        $this->assertDatabaseHas('practice_tests', [
            'title' => 'Urban Parks Reading',
            'slug' => 'urban-parks-reading',
            'skill' => 'reading',
            'is_published' => true,
        ]);
        $this->assertDatabaseHas('practice_questions', [
            'prompt' => 'Parks can support public health.',
            'correct_answer' => 'True',
        ]);
    }

    public function test_user_can_submit_published_practice_test_and_attempt_is_saved(): void
    {
        $user = User::factory()->create();
        $practiceTest = PracticeTest::create([
            'title' => 'Listening Booking',
            'slug' => 'listening-booking',
            'skill' => 'listening',
            'level' => 'intermediate',
            'duration_minutes' => 10,
            'description' => 'Booking details.',
            'transcript' => 'The booking is for Friday.',
            'is_published' => true,
        ]);
        $question = $practiceTest->questions()->create([
            'position' => 1,
            'question_type' => 'short_answer',
            'prompt' => 'Which day is the booking?',
            'correct_answer' => 'Friday',
            'explanation' => 'The transcript says Friday.',
        ]);

        $this->actingAs($user)->post("/tests/listening/{$practiceTest->slug}", [
            'answers' => [
                $question->id => ' friday ',
            ],
        ])->assertOk()
            ->assertSee('1/1', false);

        $this->assertDatabaseHas('test_attempts', [
            'user_id' => $user->id,
            'test_type' => 'IELTS Listening',
            'score' => 1,
            'total' => 1,
        ]);
    }
}
