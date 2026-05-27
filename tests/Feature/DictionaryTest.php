<?php

namespace Tests\Feature;

use App\Models\DictionaryEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            ->assertSee('1 nghĩa');

        $this->get('/dictionary/run')
            ->assertOk()
            ->assertSee('Nghĩa / ngữ cảnh tiếng Việt')
            ->assertSee('chạy')
            ->assertSee('to move fast by using your feet')
            ->assertSee('I run every morning.');
    }
}
