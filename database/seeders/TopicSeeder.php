<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topics = [
            [
                'title' => 'Describe a Person Who Inspired You',
                'slug' => 'describe-a-person-who-inspired-you',
                'description' => 'A common IELTS Speaking Part 2 topic about role models and personal impact.',
                'part' => 'Part 2',
                'difficulty' => 'Intermediate',
                'questions' => [
                    'Who is this person?',
                    'How did you know this person?',
                    'What did this person do to inspire you?',
                    'Explain how you feel about this person.',
                ],
                'sample_answer' => 'One person who has inspired me a lot is my high school English teacher, Ms. Lan. I first met her when I was in grade 10. She had a very practical teaching style and always encouraged us to think in English instead of translating from Vietnamese. What inspired me most was her consistency. She prepared every lesson carefully and gave us personal feedback after class, even when she was busy. Because of her support, I became more confident in speaking and eventually joined an English club. I really admire her dedication, and she made me believe that progress comes from disciplined daily practice.',
                'tips' => [
                    'Use past tense for background events and present tense for current feelings.',
                    'Add one concrete mini-story to make your answer more memorable.',
                ],
            ],
            [
                'title' => 'Online Learning vs Traditional Learning',
                'slug' => 'online-learning-vs-traditional-learning',
                'description' => 'Useful for IELTS Writing Task 2 discussion essays.',
                'part' => 'Writing',
                'difficulty' => 'Upper-Intermediate',
                'questions' => [
                    'What are the advantages of online learning?',
                    'What are the drawbacks of online learning?',
                    'Which method is more effective for young learners?',
                ],
                'sample_answer' => 'Online learning offers clear benefits such as flexibility and wider access to resources. Students can study at their own pace and often save commuting time. However, it can reduce face-to-face interaction and require strong self-discipline. In my view, traditional classrooms are generally more effective for younger students because direct supervision and social interaction are essential at that age. A balanced model, where schools combine classroom teaching with selected online tools, is probably the most practical long-term solution.',
                'tips' => [
                    'For Writing Task 2, keep each body paragraph focused on one main idea.',
                    'Use linking words naturally: however, moreover, in my view, therefore.',
                ],
            ],
            [
                'title' => 'Describe Your Hometown',
                'slug' => 'describe-your-hometown',
                'description' => 'A frequent IELTS Speaking Part 1 and Part 2 theme.',
                'part' => 'Part 1',
                'difficulty' => 'Beginner',
                'questions' => [
                    'Where is your hometown?',
                    'What is it famous for?',
                    'What do you like most about it?',
                    'Has it changed in recent years?',
                ],
                'sample_answer' => 'My hometown is Da Nang, a coastal city in central Vietnam. It is well known for its beaches, seafood, and clean environment. What I like most is the balance between urban life and nature. You can enjoy modern services but still reach mountains and the sea very quickly. In recent years, the city has developed much faster, with better roads and more public spaces. Even though it is more crowded now, I still think it is one of the most livable cities in the country.',
                'tips' => [
                    'Prepare a reusable hometown vocabulary set: coastal, lively, peaceful, well-connected.',
                    'Mention change over time to show range in grammar (used to, has become).',
                ],
            ],
        ];

        foreach ($topics as $topic) {
            Topic::updateOrCreate(
                ['slug' => $topic['slug']],
                $topic
            );
        }
    }
}
