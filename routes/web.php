<?php

use App\Http\Controllers\TopicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DictionaryController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PracticeTestController;
use App\Http\Controllers\VocabularyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TopicController::class, 'index'])->name('topics.index');
Route::get('/topics/{slug}', [TopicController::class, 'show'])->name('topics.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('/history', [HistoryController::class, 'index'])->middleware('auth')->name('history.index');

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
