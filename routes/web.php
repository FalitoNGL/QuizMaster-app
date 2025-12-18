<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\QuizModeController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\ResultController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Admin\QuestionController as AdminQuestionController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ImportController as AdminImportController;
use App\Http\Controllers\Admin\AnalyticsController as AdminAnalyticsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('quiz.lobby');
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return redirect()->route('quiz.lobby');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes (Laravel Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Quiz Routes (User)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Quiz Lobby
    Route::get('/quizzes', [QuizController::class, 'index'])->name('quiz.lobby');
    Route::get('/quizzes/{quiz:slug}', [QuizController::class, 'show'])->name('quiz.show');
    Route::post('/quizzes/{quiz}/start', [QuizController::class, 'start'])->name('quiz.start');

    // Exam
    Route::get('/exam/{attempt}', [ExamController::class, 'show'])->name('exam.show');
    Route::post('/exam/{attempt}/answer', [ExamController::class, 'saveAnswer'])->name('exam.answer');
    Route::post('/exam/{attempt}/submit', [ExamController::class, 'submit'])->name('exam.submit');
    Route::get('/exam/{attempt}/time', [ExamController::class, 'getTime'])->name('exam.time');
    Route::post('/exam/{attempt}/violation', [ExamController::class, 'recordViolation'])->name('exam.violation');

    // Results
    Route::get('/result/{attempt}', [ResultController::class, 'show'])->name('result.show');
    Route::get('/result/{attempt}/review', [ResultController::class, 'review'])->name('result.review');
    Route::get('/history', [ResultController::class, 'history'])->name('result.history');

    // Leaderboard
    Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

    // Quiz Mode (Mode Kuis - instant feedback)
    Route::post('/quizzes/{quiz}/start-quiz-mode', [QuizModeController::class, 'start'])->name('quiz.start-quiz-mode');
    Route::get('/quizzes/{quiz}/quiz-mode', [QuizModeController::class, 'show'])->name('quiz.quiz-mode');
    Route::post('/quizzes/{quiz}/quiz-mode/answer', [QuizModeController::class, 'answer'])->name('quiz.quiz-mode-answer');
    Route::post('/quizzes/{quiz}/quiz-mode/next', [QuizModeController::class, 'next'])->name('quiz.quiz-mode-next');
    Route::get('/quizzes/{quiz}/quiz-mode/finish', [QuizModeController::class, 'finish'])->name('quiz.quiz-mode-finish');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Quiz Management
    Route::resource('quizzes', AdminQuizController::class);

    // Question Management (nested under quiz)
    Route::resource('quizzes.questions', AdminQuestionController::class);

    // Import Questions from Excel
    Route::get('/quizzes/{quiz}/import', [AdminImportController::class, 'showForm'])->name('import.form');
    Route::post('/quizzes/{quiz}/import', [AdminImportController::class, 'import'])->name('import.process');
    Route::get('/import/template', [AdminImportController::class, 'downloadTemplate'])->name('import.template');

    // Analytics
    Route::get('/quizzes/{quiz}/analytics', [AdminAnalyticsController::class, 'show'])->name('quizzes.analytics');

    // Bulk Question Editor
    Route::get('/quizzes/{quiz}/questions/bulk', [\App\Http\Controllers\Admin\BulkQuestionController::class, 'index'])->name('questions.bulk');
    Route::post('/quizzes/{quiz}/questions/bulk-delete', [\App\Http\Controllers\Admin\BulkQuestionController::class, 'delete'])->name('questions.bulk-delete');
    Route::post('/quizzes/{quiz}/questions/bulk-move', [\App\Http\Controllers\Admin\BulkQuestionController::class, 'move'])->name('questions.bulk-move');
    Route::post('/quizzes/{quiz}/questions/bulk-points', [\App\Http\Controllers\Admin\BulkQuestionController::class, 'updatePoints'])->name('questions.bulk-points');

    // User Management
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/toggle-ban', [AdminUserController::class, 'toggleBan'])->name('users.toggle-ban');
});

require __DIR__.'/auth.php';
