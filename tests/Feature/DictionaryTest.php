<?php

namespace Tests\Feature;

use App\Models\DictionaryEntry;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DictionaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_search_dictionary_and_view_a_word(): void
    {
        DictionaryEntry::create([
            'word' => 'run',
            'normalized_word' => 'run',
            'part_of_speech' => 'verb',
            'definition' => 'to move fast by using your feet',
            'definition_vi' => '(động từ) chạy; di chuyển nhanh bằng chân',
            'examples' => ['I run every morning.'],
            'synonyms' => ['jog'],
            'source_id' => 'test-run-v',
        ]);

        $this->get('/dictionary?q=run')
            ->assertOk()
            ->assertSee('run')
            ->assertSee('1 nghĩa')
            ->assertSee('Xem đầy đủ');

        $this->get('/dictionary/run')
            ->assertOk()
            ->assertSee('Nghĩa / ngữ cảnh tiếng Việt')
            ->assertSee('chạy')
            ->assertSee('to move fast by using your feet')
            ->assertSee('I run every morning.')
            ->assertSee('jog');
    }

    public function test_dictionary_resolves_inflected_words_to_root_entries(): void
    {
        DictionaryEntry::create([
            'word' => 'run',
            'normalized_word' => 'run',
            'part_of_speech' => 'verb',
            'definition' => 'to move fast',
            'definition_vi' => 'chạy',
            'source_id' => 'test-run-root',
        ]);

        $this->get('/dictionary?q=running')
            ->assertOk()
            ->assertSee('Đã quy về từ gốc "run"', false)
            ->assertSee('Xem đầy đủ');

        $this->get('/dictionary/running')
            ->assertOk()
            ->assertSee('Từ bạn tra "running" đã được quy về từ gốc "run"', false)
            ->assertSee('chạy');
    }

    public function test_dictionary_shows_suggestions_when_no_exact_word_exists(): void
    {
        DictionaryEntry::create([
            'word' => 'policy',
            'normalized_word' => 'policy',
            'part_of_speech' => 'noun',
            'definition' => 'a plan of action',
            'definition_vi' => 'chính sách',
            'source_id' => 'test-policy-n',
        ]);

        $this->get('/dictionary?q=policx')
            ->assertOk()
            ->assertSee('Không tìm thấy mục từ phù hợp')
            ->assertSee('policy');
    }

    public function test_dictionary_live_search_returns_results_partial(): void
    {
        DictionaryEntry::create([
            'word' => 'education',
            'normalized_word' => 'education',
            'part_of_speech' => 'noun',
            'definition' => 'the process of learning',
            'definition_vi' => 'giáo dục',
            'source_id' => 'test-education-n',
        ]);

        $this->get('/dictionary/search?q=education', [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('education')
            ->assertSee('Xem đầy đủ')
            ->assertDontSee('<html', false);
    }

    public function test_dictionary_live_search_uses_vocabulary_when_wordnet_entry_is_missing(): void
    {
        Vocabulary::create([
            'word' => 'mindset',
            'phonetic' => '/test/',
            'part_of_speech' => 'noun',
            'meaning_vi' => 'tư duy',
            'definition_en' => 'a way of thinking',
            'example_en' => 'A growth mindset helps learners improve.',
            'example_vi' => 'Tư duy phát triển giúp người học tiến bộ.',
            'topic' => 'Education',
            'level' => 'B2',
        ]);

        $this->get('/dictionary/search?q=mindset', [
            'X-Requested-With' => 'XMLHttpRequest',
        ])
            ->assertOk()
            ->assertSee('Nghĩa tiếng Việt')
            ->assertSee('tư duy')
            ->assertSee('A growth mindset helps learners improve.');
    }

    public function test_dictionary_translate_calls_google_translate_api(): void
    {
        config(['services.google_translate.key' => 'test-key']);

        Http::fake([
            'https://translation.googleapis.com/*' => Http::response([
                'data' => [
                    'translations' => [
                        [
                            'translatedText' => 'Xin chào thế giới',
                            'detectedSourceLanguage' => 'en',
                        ],
                    ],
                ],
            ]),
        ]);

        $this->postJson('/dictionary/translate', [
            'q' => 'Hello world',
            'source' => 'en',
            'target' => 'vi',
        ])
            ->assertOk()
            ->assertJson([
                'translatedText' => 'Xin chào thế giới',
                'detectedSourceLanguage' => 'en',
            ]);

        Http::assertSent(fn ($request) => $request->url() === 'https://translation.googleapis.com/language/translate/v2?key=test-key'
            && $request['q'] === 'Hello world'
            && $request['source'] === 'en'
            && $request['target'] === 'vi');
    }

    public function test_dictionary_translate_requires_google_translate_api_key(): void
    {
        config(['services.google_translate.key' => null]);

        $this->postJson('/dictionary/translate', [
            'q' => 'Hello world',
            'source' => 'en',
            'target' => 'vi',
        ])
            ->assertStatus(503)
            ->assertJsonPath('message', 'Chưa cấu hình GOOGLE_TRANSLATE_API_KEY trong .env. Hiện chỉ dịch nội bộ được các từ/cụm từ có trong dữ liệu.');
    }

    public function test_dictionary_translate_falls_back_to_local_vocabulary_without_api_key(): void
    {
        config(['services.google_translate.key' => null]);

        Vocabulary::create([
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

        $this->postJson('/dictionary/translate', [
            'q' => 'sustainable',
            'source' => 'en',
            'target' => 'vi',
        ])
            ->assertOk()
            ->assertJson([
                'translatedText' => 'bền vững',
                'detectedSourceLanguage' => 'local',
                'source' => 'IELTS Focus vocabulary',
            ]);
    }

    public function test_dictionary_show_includes_ielts_vocabulary_context_and_history(): void
    {
        $user = User::factory()->create();

        DictionaryEntry::create([
            'word' => 'sustainable',
            'normalized_word' => 'sustainable',
            'part_of_speech' => 'adjective',
            'definition' => 'able to continue for a long time',
            'definition_vi' => 'bền vững',
            'source_id' => 'test-sustainable-adj',
        ]);

        Vocabulary::create([
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

        $this->actingAs($user)
            ->get('/dictionary/sustainable')
            ->assertOk()
            ->assertSee('IELTS vocabulary')
            ->assertSee('Nghĩa học IELTS')
            ->assertSee('Sustainable transport is important.');

        $this->assertDatabaseHas('word_lookup_histories', [
            'user_id' => $user->id,
            'word' => 'sustainable',
            'normalized_word' => 'sustainable',
            'senses_count' => 1,
        ]);
    }
}
