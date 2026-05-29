<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\PracticeTest;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', [
            'stats' => [
                'topics' => Topic::count(),
                'practiceTests' => PracticeTest::count(),
                'vocabularies' => Vocabulary::count(),
                'users' => User::count(),
                'faqs' => Faq::count(),
            ],
            'recentTopics' => Topic::latest()->take(5)->get(),
            'recentPracticeTests' => PracticeTest::latest()->take(5)->get(),
            'recentWords' => Vocabulary::latest()->take(5)->get(),
        ]);
    }

    public function topics()
    {
        return view('admin.topics.index', [
            'topics' => Topic::orderBy('part')->orderBy('title')->paginate(20),
        ]);
    }

    public function createTopic()
    {
        return view('admin.topics.form', [
            'topic' => new Topic(),
            'action' => route('admin.topics.store'),
            'method' => 'POST',
        ]);
    }

    public function storeTopic(Request $request)
    {
        Topic::create($this->topicData($request));

        return redirect()->route('admin.topics.index')->with('status', 'Đã tạo topic mới.');
    }

    public function editTopic(Topic $topic)
    {
        return view('admin.topics.form', [
            'topic' => $topic,
            'action' => route('admin.topics.update', $topic),
            'method' => 'PUT',
        ]);
    }

    public function updateTopic(Request $request, Topic $topic)
    {
        $topic->update($this->topicData($request, $topic));

        return redirect()->route('admin.topics.index')->with('status', 'Đã cập nhật topic.');
    }

    public function destroyTopic(Topic $topic)
    {
        $topic->delete();

        return redirect()->route('admin.topics.index')->with('status', 'Đã xóa topic.');
    }

    public function vocabularies(Request $request)
    {
        $search = trim((string) $request->query('q'));

        $words = Vocabulary::query()
            ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('word', 'like', "%{$search}%")
                    ->orWhere('meaning_vi', 'like', "%{$search}%")
                    ->orWhere('topic', 'like', "%{$search}%");
            }))
            ->orderBy('word')
            ->paginate(30)
            ->withQueryString();

        return view('admin.vocabularies.index', compact('words', 'search'));
    }

    public function createVocabulary()
    {
        return view('admin.vocabularies.form', [
            'vocabulary' => new Vocabulary(),
            'action' => route('admin.vocabularies.store'),
            'method' => 'POST',
        ]);
    }

    public function storeVocabulary(Request $request)
    {
        Vocabulary::create($this->vocabularyData($request));

        return redirect()->route('admin.vocabularies.index')->with('status', 'Đã tạo từ vựng mới.');
    }

    public function editVocabulary(Vocabulary $vocabulary)
    {
        return view('admin.vocabularies.form', [
            'vocabulary' => $vocabulary,
            'action' => route('admin.vocabularies.update', $vocabulary),
            'method' => 'PUT',
        ]);
    }

    public function updateVocabulary(Request $request, Vocabulary $vocabulary)
    {
        $vocabulary->update($this->vocabularyData($request, $vocabulary));

        return redirect()->route('admin.vocabularies.index')->with('status', 'Đã cập nhật từ vựng.');
    }

    public function destroyVocabulary(Vocabulary $vocabulary)
    {
        $vocabulary->delete();

        return redirect()->route('admin.vocabularies.index')->with('status', 'Đã xóa từ vựng.');
    }

    public function practiceTests(Request $request)
    {
        $skill = $request->query('skill');

        return view('admin.practice-tests.index', [
            'tests' => PracticeTest::query()
                ->when(in_array($skill, ['reading', 'listening', 'writing', 'speaking'], true), fn ($query) => $query->where('skill', $skill))
                ->withCount('questions')
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'skill' => $skill,
        ]);
    }

    public function createPracticeTest()
    {
        return view('admin.practice-tests.form', [
            'practiceTest' => new PracticeTest([
                'skill' => 'reading',
                'level' => 'intermediate',
                'duration_minutes' => 20,
                'is_published' => true,
            ]),
            'questions' => collect(),
            'action' => route('admin.practice-tests.store'),
            'method' => 'POST',
        ]);
    }

    public function storePracticeTest(Request $request)
    {
        $data = $this->practiceTestData($request);
        $data['audio_path'] = $this->storeAudio($request);

        $practiceTest = PracticeTest::create($data);
        $this->syncPracticeQuestions($practiceTest, $request);

        return redirect()->route('admin.practice-tests.index')->with('status', 'Đã tạo đề luyện tập.');
    }

    public function editPracticeTest(PracticeTest $practiceTest)
    {
        return view('admin.practice-tests.form', [
            'practiceTest' => $practiceTest,
            'questions' => $practiceTest->questions()->get(),
            'action' => route('admin.practice-tests.update', $practiceTest),
            'method' => 'PUT',
        ]);
    }

    public function updatePracticeTest(Request $request, PracticeTest $practiceTest)
    {
        $data = $this->practiceTestData($request, $practiceTest);
        $audioPath = $this->storeAudio($request);

        if ($audioPath) {
            if ($practiceTest->audio_path) {
                Storage::disk('public')->delete($practiceTest->audio_path);
            }

            $data['audio_path'] = $audioPath;
        }

        $practiceTest->update($data);
        $this->syncPracticeQuestions($practiceTest, $request);

        return redirect()->route('admin.practice-tests.index')->with('status', 'Đã cập nhật đề luyện tập.');
    }

    public function destroyPracticeTest(PracticeTest $practiceTest)
    {
        if ($practiceTest->audio_path) {
            Storage::disk('public')->delete($practiceTest->audio_path);
        }

        $practiceTest->delete();

        return redirect()->route('admin.practice-tests.index')->with('status', 'Đã xóa đề luyện tập.');
    }

    public function faqs()
    {
        return view('admin.faqs.index', [
            'faqs' => Faq::orderBy('position')->orderBy('id')->paginate(20),
        ]);
    }

    public function createFaq()
    {
        return view('admin.faqs.form', [
            'faq' => new Faq([
                'position' => (Faq::max('position') ?? 0) + 1,
                'is_published' => true,
            ]),
            'action' => route('admin.faqs.store'),
            'method' => 'POST',
        ]);
    }

    public function storeFaq(Request $request)
    {
        Faq::create($this->faqData($request));

        return redirect()->route('admin.faqs.index')->with('status', 'Đã tạo câu hỏi thường gặp.');
    }

    public function editFaq(Faq $faq)
    {
        return view('admin.faqs.form', [
            'faq' => $faq,
            'action' => route('admin.faqs.update', $faq),
            'method' => 'PUT',
        ]);
    }

    public function updateFaq(Request $request, Faq $faq)
    {
        $faq->update($this->faqData($request));

        return redirect()->route('admin.faqs.index')->with('status', 'Đã cập nhật câu hỏi thường gặp.');
    }

    public function destroyFaq(Faq $faq)
    {
        $faq->delete();

        return redirect()->route('admin.faqs.index')->with('status', 'Đã xóa câu hỏi thường gặp.');
    }

    private function topicData(Request $request, ?Topic $topic = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('topics', 'slug')->ignore($topic)],
            'description' => ['required', 'string'],
            'part' => ['required', 'string', 'max:20'],
            'difficulty' => ['required', 'string', 'max:20'],
            'questions_text' => ['required', 'string'],
            'sample_answer' => ['required', 'string'],
            'tips_text' => ['nullable', 'string'],
        ]);

        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?: Str::slug($data['title']),
            'description' => $data['description'],
            'part' => $data['part'],
            'difficulty' => $data['difficulty'],
            'questions' => $this->lines($data['questions_text']),
            'sample_answer' => $data['sample_answer'],
            'tips' => $this->lines($data['tips_text'] ?? ''),
        ];
    }

    private function vocabularyData(Request $request, ?Vocabulary $vocabulary = null): array
    {
        $data = $request->validate([
            'word' => ['required', 'string', 'max:255', Rule::unique('vocabularies', 'word')->ignore($vocabulary)],
            'phonetic' => ['nullable', 'string', 'max:255'],
            'part_of_speech' => ['required', 'string', 'max:255'],
            'meaning_vi' => ['required', 'string'],
            'definition_en' => ['required', 'string'],
            'example_en' => ['required', 'string'],
            'example_vi' => ['required', 'string'],
            'topic' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:20'],
            'synonyms_text' => ['nullable', 'string'],
        ]);

        return [
            'word' => $data['word'],
            'phonetic' => $data['phonetic'] ?? null,
            'part_of_speech' => $data['part_of_speech'],
            'meaning_vi' => $data['meaning_vi'],
            'definition_en' => $data['definition_en'],
            'example_en' => $data['example_en'],
            'example_vi' => $data['example_vi'],
            'topic' => $data['topic'] ?? null,
            'level' => $data['level'],
            'synonyms' => $this->items($data['synonyms_text'] ?? ''),
        ];
    }

    private function faqData(Request $request): array
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string', 'max:5000'],
            'position' => ['required', 'integer', 'min:0', 'max:999'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return [
            'question' => $data['question'],
            'answer' => $data['answer'],
            'position' => $data['position'],
            'is_published' => $request->boolean('is_published'),
        ];
    }

    private function practiceTestData(Request $request, ?PracticeTest $practiceTest = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('practice_tests', 'slug')->ignore($practiceTest)],
            'skill' => ['required', Rule::in(['reading', 'listening', 'writing', 'speaking'])],
            'level' => ['required', 'string', 'max:50'],
            'duration_minutes' => ['required', 'integer', 'min:1', 'max:180'],
            'description' => ['nullable', 'string'],
            'passage' => ['nullable', 'string'],
            'transcript' => ['nullable', 'string'],
            'audio_file' => ['nullable', 'file', 'mimes:mp3,wav,ogg,m4a,mp4', 'max:20480'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        return [
            'title' => $data['title'],
            'slug' => $data['slug'] ?: Str::slug($data['title']),
            'skill' => $data['skill'],
            'level' => $data['level'],
            'duration_minutes' => $data['duration_minutes'],
            'description' => $data['description'] ?? null,
            'passage' => $data['passage'] ?? null,
            'transcript' => $data['transcript'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ];
    }

    private function syncPracticeQuestions(PracticeTest $practiceTest, Request $request): void
    {
        $data = $request->validate([
            'questions' => ['required', 'array', 'min:1'],
            'questions.*.prompt' => ['nullable', 'string', 'max:2000'],
            'questions.*.question_type' => ['nullable', Rule::in(['multiple_choice', 'short_answer'])],
            'questions.*.options_text' => ['nullable', 'string', 'max:2000'],
            'questions.*.correct_answer' => ['nullable', 'string', 'max:1000'],
            'questions.*.explanation' => ['nullable', 'string', 'max:3000'],
        ]);

        $questions = collect($data['questions'])
            ->map(function (array $question, int $index) {
                $prompt = trim((string) ($question['prompt'] ?? ''));
                $correctAnswer = trim((string) ($question['correct_answer'] ?? ''));

                if ($prompt === '' || $correctAnswer === '') {
                    return null;
                }

                return [
                    'position' => $index + 1,
                    'question_type' => $question['question_type'] ?? 'multiple_choice',
                    'prompt' => $prompt,
                    'options' => $this->lines($question['options_text'] ?? ''),
                    'correct_answer' => $correctAnswer,
                    'explanation' => trim((string) ($question['explanation'] ?? '')) ?: null,
                ];
            })
            ->filter()
            ->values();

        abort_if($questions->isEmpty(), 422, 'Cần ít nhất 1 câu hỏi có đầy đủ đề bài và đáp án.');

        $practiceTest->questions()->delete();
        $practiceTest->questions()->createMany($questions->all());
    }

    private function storeAudio(Request $request): ?string
    {
        if (! $request->hasFile('audio_file')) {
            return null;
        }

        return $request->file('audio_file')->store('practice-audio', 'public');
    }

    public function users(Request $request)
    {
        $search = trim((string) $request->query('q'));

        return view('admin.users.index', [
            'users' => User::query()
                ->withCount('testAttempts')
                ->when($search, fn ($query) => $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                }))
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'search' => $search,
        ]);
    }

    public function toggleAdmin(User $user)
    {
        abort_if($user->is(auth()->user()), 422, 'Không thể tự đổi quyền của chính mình.');

        $user->update(['is_admin' => ! $user->is_admin]);

        return back()->with('status', 'Đã cập nhật quyền user.');
    }

    public function submissions(Request $request)
    {
        $status = $request->query('status', 'pending');

        return view('admin.submissions.index', [
            'submissions' => TestAttempt::query()
                ->with(['user', 'reviewer'])
                ->whereIn('test_type', ['IELTS Writing', 'IELTS Speaking'])
                ->when($status === 'pending', fn ($query) => $query->whereNull('reviewed_at'))
                ->when($status === 'reviewed', fn ($query) => $query->whereNotNull('reviewed_at'))
                ->latest()
                ->paginate(20)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function editSubmission(TestAttempt $testAttempt)
    {
        abort_unless(in_array($testAttempt->test_type, ['IELTS Writing', 'IELTS Speaking'], true), 404);

        return view('admin.submissions.form', [
            'submission' => $testAttempt->load(['user', 'reviewer']),
        ]);
    }

    public function updateSubmission(Request $request, TestAttempt $testAttempt)
    {
        abort_unless(in_array($testAttempt->test_type, ['IELTS Writing', 'IELTS Speaking'], true), 404);

        $data = $request->validate([
            'band_score' => ['required', 'numeric', 'min:0', 'max:9'],
            'feedback' => ['required', 'string', 'max:5000'],
            'task_response' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'coherence' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'lexical_resource' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'grammar' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'fluency' => ['nullable', 'numeric', 'min:0', 'max:9'],
            'pronunciation' => ['nullable', 'numeric', 'min:0', 'max:9'],
        ]);

        $testAttempt->update([
            'band_score' => $data['band_score'],
            'feedback' => $data['feedback'],
            'criteria_scores' => collect($data)
                ->only(['task_response', 'coherence', 'lexical_resource', 'grammar', 'fluency', 'pronunciation'])
                ->filter(fn ($value) => $value !== null)
                ->all(),
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.submissions.index')->with('status', 'Đã gửi feedback cho học viên.');
    }

    private function lines(string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', $value) ?: [])
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    private function items(string $value): array
    {
        return collect(preg_split('/,|\r\n|\r|\n/', $value) ?: [])
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
