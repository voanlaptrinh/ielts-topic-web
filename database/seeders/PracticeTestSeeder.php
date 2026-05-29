<?php

namespace Database\Seeders;

use App\Models\PracticeTest;
use Illuminate\Database\Seeder;

class PracticeTestSeeder extends Seeder
{
    public function run(): void
    {
        $reading = PracticeTest::updateOrCreate(
            ['slug' => 'urban-green-spaces-reading'],
            [
                'title' => 'Urban Green Spaces',
                'skill' => 'reading',
                'level' => 'intermediate',
                'duration_minutes' => 12,
                'description' => 'Luyện True/False/Not Given và ý chính với passage ngắn.',
                'passage' => 'Urban green spaces can improve public health by encouraging exercise and reducing stress. However, access to parks is often unequal, especially in rapidly growing cities. Some researchers argue that city planning should treat parks as essential infrastructure rather than optional decoration.',
                'is_published' => true,
            ],
        );

        $reading->questions()->delete();
        $reading->questions()->createMany([
            [
                'position' => 1,
                'question_type' => 'multiple_choice',
                'prompt' => 'Green spaces may improve public health.',
                'options' => ['True', 'False', 'Not Given'],
                'correct_answer' => 'True',
                'explanation' => 'The passage says green spaces can improve public health.',
            ],
            [
                'position' => 2,
                'question_type' => 'multiple_choice',
                'prompt' => 'All citizens have equal access to parks.',
                'options' => ['True', 'False', 'Not Given'],
                'correct_answer' => 'False',
                'explanation' => 'The passage says access is often unequal.',
            ],
        ]);

        $listening = PracticeTest::updateOrCreate(
            ['slug' => 'study-room-booking-listening'],
            [
                'title' => 'Study Room Booking',
                'skill' => 'listening',
                'level' => 'foundation',
                'duration_minutes' => 8,
                'description' => 'Luyện nghe thông tin ngắn: ngày, thiết bị, số lượng người.',
                'transcript' => 'Good morning. I would like to book a study room for Friday afternoon. The room should have a projector and space for six students.',
                'is_published' => true,
            ],
        );

        $listening->questions()->delete();
        $listening->questions()->createMany([
            [
                'position' => 1,
                'question_type' => 'short_answer',
                'prompt' => 'Which day is the room booked for?',
                'options' => [],
                'correct_answer' => 'Friday',
                'explanation' => 'The speaker says Friday afternoon.',
            ],
            [
                'position' => 2,
                'question_type' => 'short_answer',
                'prompt' => 'What equipment should the room have?',
                'options' => [],
                'correct_answer' => 'projector',
                'explanation' => 'The speaker asks for a projector.',
            ],
        ]);
    }
}
