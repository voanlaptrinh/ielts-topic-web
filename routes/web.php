<?php

use App\Http\Controllers\TopicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PracticeTestController;
use App\Http\Controllers\PrepHubController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VocabularyController;
use App\Models\PracticeTest;
use App\Models\Topic;
use App\Models\Vocabulary;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Route::get('/', [TopicController::class, 'index'])->name('topics.index');
Route::get('/topics/{slug}', [TopicController::class, 'show'])->name('topics.show');

Route::get('/sitemap.xml', function () {
    $staticUrls = collect([
        route('topics.index'),
        route('vocabularies.index'),
        route('vocabularies.flashcards'),
        route('vocabularies.quiz'),
        route('dictionary.index'),
        route('prep.index'),
        route('search.index'),
        route('tests.index'),
        route('tests.reading'),
        route('tests.listening'),
        route('tests.writing'),
        route('tests.speaking'),
    ]);

    $levelUrls = collect([
        'foundation',
        'elementary',
        'pre-intermediate',
        'intermediate',
        'upper-intermediate',
        'advanced',
    ])->map(fn (string $level) => route('tests.level', $level));

    $topicUrls = Topic::orderBy('slug')
        ->pluck('slug')
        ->map(fn (string $slug) => route('topics.show', $slug));

    $vocabularyUrls = Vocabulary::orderBy('word')
        ->pluck('word')
        ->map(fn (string $word) => route('vocabularies.show', $word));

    $practiceUrls = PracticeTest::where('is_published', true)
        ->orderBy('skill')
        ->orderBy('slug')
        ->get(['skill', 'slug'])
        ->map(fn (PracticeTest $test) => route('tests.practice.show', [$test->skill, $test]));

    $urls = $staticUrls
        ->merge($levelUrls)
        ->merge($topicUrls)
        ->merge($practiceUrls)
        ->merge($vocabularyUrls)
        ->unique()
        ->values();

    $xml = view('sitemap', ['urls' => $urls])->render();

    return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
})->name('sitemap');

Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/ielts-prep', [PrepHubController::class, 'index'])->name('prep.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::put('/dashboard/goal', [DashboardController::class, 'updateGoal'])->middleware('auth')->name('dashboard.goal');
Route::get('/history', [HistoryController::class, 'index'])->middleware('auth')->name('history.index');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/topics', [AdminController::class, 'topics'])->name('topics.index');
    Route::get('/topics/create', [AdminController::class, 'createTopic'])->name('topics.create');
    Route::post('/topics', [AdminController::class, 'storeTopic'])->name('topics.store');
    Route::get('/topics/{topic}/edit', [AdminController::class, 'editTopic'])->name('topics.edit');
    Route::put('/topics/{topic}', [AdminController::class, 'updateTopic'])->name('topics.update');
    Route::delete('/topics/{topic}', [AdminController::class, 'destroyTopic'])->name('topics.destroy');
    Route::get('/practice-tests', [AdminController::class, 'practiceTests'])->name('practice-tests.index');
    Route::get('/practice-tests/create', [AdminController::class, 'createPracticeTest'])->name('practice-tests.create');
    Route::post('/practice-tests', [AdminController::class, 'storePracticeTest'])->name('practice-tests.store');
    Route::get('/practice-tests/{practiceTest}/edit', [AdminController::class, 'editPracticeTest'])->name('practice-tests.edit');
    Route::put('/practice-tests/{practiceTest}', [AdminController::class, 'updatePracticeTest'])->name('practice-tests.update');
    Route::delete('/practice-tests/{practiceTest}', [AdminController::class, 'destroyPracticeTest'])->name('practice-tests.destroy');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::put('/users/{user}/toggle-admin', [AdminController::class, 'toggleAdmin'])->name('users.toggle-admin');
    Route::get('/submissions', [AdminController::class, 'submissions'])->name('submissions.index');
    Route::get('/submissions/{testAttempt}/review', [AdminController::class, 'editSubmission'])->name('submissions.edit');
    Route::put('/submissions/{testAttempt}', [AdminController::class, 'updateSubmission'])->name('submissions.update');
    Route::get('/vocabularies', [AdminController::class, 'vocabularies'])->name('vocabularies.index');
    Route::get('/vocabularies/create', [AdminController::class, 'createVocabulary'])->name('vocabularies.create');
    Route::post('/vocabularies', [AdminController::class, 'storeVocabulary'])->name('vocabularies.store');
    Route::get('/vocabularies/{vocabulary}/edit', [AdminController::class, 'editVocabulary'])->name('vocabularies.edit');
    Route::put('/vocabularies/{vocabulary}', [AdminController::class, 'updateVocabulary'])->name('vocabularies.update');
    Route::delete('/vocabularies/{vocabulary}', [AdminController::class, 'destroyVocabulary'])->name('vocabularies.destroy');
});

Route::get('/vocabulary', [VocabularyController::class, 'index'])->name('vocabularies.index');
Route::get('/vocabulary/search', [VocabularyController::class, 'search'])->name('vocabularies.search');
Route::get('/vocabulary/flashcards', [VocabularyController::class, 'flashcards'])->name('vocabularies.flashcards');
Route::get('/vocabulary/quiz', [VocabularyController::class, 'quiz'])->name('vocabularies.quiz');
Route::post('/vocabulary/quiz', [VocabularyController::class, 'submitQuiz'])->name('vocabularies.quiz.submit');
Route::get('/vocabulary/{word}', [VocabularyController::class, 'show'])->name('vocabularies.show');

Route::get('/dictionary', [DictionaryController::class, 'index'])->name('dictionary.index');
Route::get('/dictionary/search', [DictionaryController::class, 'search'])->name('dictionary.search');
Route::post('/dictionary/translate', [DictionaryController::class, 'translate'])->name('dictionary.translate');
Route::get('/dictionary/{word}', [DictionaryController::class, 'show'])->name('dictionary.show');

Route::get('/tests', [PracticeTestController::class, 'index'])->name('tests.index');
Route::get('/tests/reading', [PracticeTestController::class, 'reading'])->name('tests.reading');
Route::get('/tests/listening', [PracticeTestController::class, 'listening'])->name('tests.listening');
Route::get('/tests/writing', [PracticeTestController::class, 'writing'])->name('tests.writing');
Route::get('/tests/speaking', [PracticeTestController::class, 'speaking'])->name('tests.speaking');
Route::get('/tests/{skill}/{practiceTest}', [PracticeTestController::class, 'showPracticeTest'])->whereIn('skill', ['reading', 'listening', 'writing', 'speaking'])->name('tests.practice.show');
Route::post('/tests/{skill}/{practiceTest}', [PracticeTestController::class, 'submitPracticeTest'])->whereIn('skill', ['reading', 'listening', 'writing', 'speaking'])->name('tests.practice.submit');
Route::get('/tests/levels/{level}', [PracticeTestController::class, 'level'])->name('tests.level');
Route::get('/tests/levels/{level}/vocabulary', [PracticeTestController::class, 'vocabulary'])->name('tests.vocabulary');
Route::post('/tests/levels/{level}/vocabulary', [PracticeTestController::class, 'submitVocabulary'])->name('tests.vocabulary.submit');
Route::get('/tests/levels/{level}/grammar', [PracticeTestController::class, 'grammar'])->name('tests.grammar');
Route::post('/tests/levels/{level}/grammar', [PracticeTestController::class, 'submitGrammar'])->name('tests.grammar.submit');
Route::get('/tests/levels/{level}/sentence-role', [PracticeTestController::class, 'sentenceRole'])->name('tests.sentence-role');
Route::post('/tests/levels/{level}/sentence-role', [PracticeTestController::class, 'submitSentenceRole'])->name('tests.sentence-role.submit');
Route::get('/tests/levels/{level}/definition', [PracticeTestController::class, 'definition'])->name('tests.definition');
Route::post('/tests/levels/{level}/definition', [PracticeTestController::class, 'submitDefinition'])->name('tests.definition.submit');
Route::get('/tests/levels/{level}/spelling', [PracticeTestController::class, 'spelling'])->name('tests.spelling');
Route::post('/tests/levels/{level}/spelling', [PracticeTestController::class, 'submitSpelling'])->name('tests.spelling.submit');
Route::get('/tests/levels/{level}/example-completion', [PracticeTestController::class, 'exampleCompletion'])->name('tests.example-completion');
Route::post('/tests/levels/{level}/example-completion', [PracticeTestController::class, 'submitExampleCompletion'])->name('tests.example-completion.submit');
Route::get('/tests/levels/{level}/ielts-format', [PracticeTestController::class, 'ieltsFormat'])->name('tests.ielts-format');
Route::post('/tests/levels/{level}/ielts-format', [PracticeTestController::class, 'submitIeltsFormat'])->name('tests.ielts-format.submit');
Route::get('/tests/levels/{level}/skills/{skill}', [PracticeTestController::class, 'skillPractice'])->name('tests.skill');
Route::post('/tests/levels/{level}/skills/{skill}', [PracticeTestController::class, 'submitSkillPractice'])->name('tests.skill.submit');
