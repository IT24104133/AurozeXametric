<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\StudentRegisterController;

use App\Http\Controllers\PasswordChangeController;
use App\Http\Controllers\ProfileController;

use App\Http\Controllers\Admin\StudentBulkController;
use App\Http\Controllers\Admin\ExamController;
use App\Http\Controllers\Admin\ExamQuestionController;
use App\Http\Controllers\Admin\ExamResultController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPastPaperSubjectController;
use App\Http\Controllers\Admin\AdminPastPaperController;
use App\Http\Controllers\Admin\AdminPastPaperQuestionController;
use App\Http\Controllers\Admin\AdminQuestionBankController;
use App\Http\Controllers\Admin\AdminHealthController;
use App\Http\Controllers\Admin\AdminQuestionHealthController;
use App\Http\Controllers\Admin\AdminSubjectSettingsController;
use App\Http\Controllers\Admin\AdminCoinTransactionController;
use App\Http\Controllers\Admin\AdminHomepageSettingsController;

use App\Http\Controllers\Student\StudentExamController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentAttemptController;
use App\Http\Controllers\Student\StudentPastPaperController;
use App\Http\Controllers\Student\StudentPastPaperAttemptController;

Route::get('/', [WelcomeController::class, 'index'])->name('home');

// Dashboard redirect based on role
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    return match(auth()->user()->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'student' => redirect()->route('student.dashboard'),
        'teacher' => redirect()->route('login'), // Redirect teachers to login for now
        default => redirect()->route('login'),
    };
})->name('dashboard');


// =========================
// AUTH (GUEST ONLY)
// =========================
Route::middleware('guest')->group(function () {

    // Login
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    // Student Self Register
    Route::get('/register', [StudentRegisterController::class, 'show'])->name('register');
    Route::post('/register', [StudentRegisterController::class, 'store'])->name('register.store');
    Route::get('/register/success', [StudentRegisterController::class, 'success'])->name('register.success');
});


// Logout (AUTH ONLY)
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');


// =========================
// AUTH PROTECTED ROUTES
// =========================
Route::middleware(['auth'])->group(function () {

    // Password change
    Route::get('/change-password', [PasswordChangeController::class, 'edit'])->name('password.change');
    Route::post('/change-password', [PasswordChangeController::class, 'update'])->name('password.update');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');


    // =========================
    // ADMIN
    // =========================
    Route::prefix('admin')->name('admin.')->middleware(['role:admin'])->group(function () {

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        
        // Homepage Settings
        Route::get('/homepage-settings', [AdminHomepageSettingsController::class, 'index'])->name('homepage.settings');
        Route::post('/homepage-settings/draft', [AdminHomepageSettingsController::class, 'saveDraft'])->name('homepage.settings.draft');
        Route::post('/homepage-settings/publish', [AdminHomepageSettingsController::class, 'publish'])->name('homepage.settings.publish');
        Route::get('/homepage-preview', [AdminHomepageSettingsController::class, 'preview'])->name('homepage.preview');
        
        // System Health
        Route::get('/system-health', [AdminHealthController::class, 'index'])->name('system.health');
        
        // Question Bank Health
        Route::get('/question-health', [AdminQuestionHealthController::class, 'index'])->name('question_health');

        // Exams
        Route::get('/exams', [ExamController::class, 'index'])->name('exams.index');
        Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
        Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
        Route::get('/exams/{exam}/edit', [ExamController::class, 'edit'])->name('exams.edit');
        Route::put('/exams/{exam}', [ExamController::class, 'update'])->name('exams.update');
        Route::delete('/exams/{exam}', [ExamController::class, 'destroy'])->name('exams.destroy');

        Route::post('/exams/{exam}/publish', [ExamController::class, 'publish'])->name('exams.publish');
        Route::post('/exams/{exam}/results-toggle', [ExamController::class, 'toggleResults'])->name('exams.results.toggle');

        Route::get('/exams/{exam}/results', [ExamResultController::class, 'index'])
            ->name('exams.results.index');

        Route::prefix('exams/{exam}')->name('exams.')->group(function () {

            Route::get('questions', [ExamQuestionController::class, 'index'])->name('questions.index');
            Route::get('questions/create', [ExamQuestionController::class, 'create'])->name('questions.create');
            Route::post('questions', [ExamQuestionController::class, 'store'])->name('questions.store');

            Route::get('questions/{question}/edit', [ExamQuestionController::class, 'edit'])->name('questions.edit');
            Route::put('questions/{question}', [ExamQuestionController::class, 'update'])->name('questions.update');
            Route::delete('questions/{question}', [ExamQuestionController::class, 'destroy'])->name('questions.destroy');

            Route::post('questions/{question}/toggle-include', [ExamQuestionController::class, 'toggleInclude'])
                ->name('questions.toggleInclude');
        });

        // Student bulk create
        Route::get('/students/bulk-create', [StudentBulkController::class, 'create'])->name('students.bulk.create');
        Route::post('/students/bulk-create', [StudentBulkController::class, 'store'])->name('students.bulk.store');

        // Past Papers Management (stream-based)
        Route::prefix('past-papers')->name('past_papers.')->group(function () {
            // Home page with 3 stream cards
            Route::get('/', [AdminPastPaperController::class, 'home'])->name('home');
            
            // Question Bank Health Check
            Route::get('/health', [AdminQuestionBankController::class, 'health'])->name('health');
            
            // Stream-based routes
            Route::prefix('{stream}')->group(function () {
                // Subjects
                Route::get('/subjects', [AdminPastPaperSubjectController::class, 'index'])->name('subjects.index');
                Route::get('/subjects/create', [AdminPastPaperSubjectController::class, 'create'])->name('subjects.create');
                Route::post('/subjects', [AdminPastPaperSubjectController::class, 'store'])->name('subjects.store');
                Route::get('/subjects/{subject}/edit', [AdminPastPaperSubjectController::class, 'edit'])->name('subjects.edit');
                Route::put('/subjects/{subject}', [AdminPastPaperSubjectController::class, 'update'])->name('subjects.update');
                Route::delete('/subjects/{subject}', [AdminPastPaperSubjectController::class, 'destroy'])->name('subjects.destroy');
                
                // Subject Settings (Free Style Configuration)
                Route::get('/subjects/{subject}/settings', [AdminSubjectSettingsController::class, 'show'])->name('subjects.settings');
                Route::put('/subjects/{subject}/settings', [AdminSubjectSettingsController::class, 'update'])->name('subjects.settings.update');

                // Papers
                Route::get('/subjects/{subject}/papers', [AdminPastPaperController::class, 'index'])->name('papers.index');
                Route::get('/papers/create', [AdminPastPaperController::class, 'create'])->name('papers.create');
                Route::post('/papers', [AdminPastPaperController::class, 'store'])->name('papers.store');
                Route::get('/papers/{paper}/edit', [AdminPastPaperController::class, 'edit'])->name('papers.edit');
                Route::put('/papers/{paper}', [AdminPastPaperController::class, 'update'])->name('papers.update');
                Route::delete('/papers/{paper}', [AdminPastPaperController::class, 'destroy'])->name('papers.destroy');
                Route::post('/papers/{paper}/toggle-publish', [AdminPastPaperController::class, 'togglePublish'])->name('papers.toggle_publish');

                // Questions
                Route::get('/papers/{paper}/questions', [AdminPastPaperQuestionController::class, 'index'])->name('questions.index');
                Route::get('/papers/{paper}/questions/create', [AdminPastPaperQuestionController::class, 'create'])->name('questions.create');
                Route::post('/papers/{paper}/questions', [AdminPastPaperQuestionController::class, 'store'])->name('questions.store');
                Route::get('/questions/{question}/edit', [AdminPastPaperQuestionController::class, 'edit'])->name('questions.edit');
                Route::put('/questions/{question}', [AdminPastPaperQuestionController::class, 'update'])->name('questions.update');
                Route::delete('/questions/{question}', [AdminPastPaperQuestionController::class, 'destroy'])->name('questions.destroy');
            });
        });

        // Coin Transactions Audit
        Route::get('/coins/transactions', [AdminCoinTransactionController::class, 'index'])->name('coins.transactions.index');
    });


    // =========================
    // STUDENT
    // =========================
    Route::prefix('student')->name('student.')
        ->middleware(['role:student'])
        ->group(function () {

            Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

            // Past Papers (stream-based)
            Route::get('/past-papers', [StudentPastPaperController::class, 'home'])->name('past_papers.home');
            Route::get('/past-papers/{stream}', [StudentPastPaperController::class, 'streams'])->name('past_papers.streams');
            Route::get('/past-papers/{stream}/subject/{subject}', [StudentPastPaperController::class, 'subject'])->name('past_papers.subject');
            Route::get('/past-papers/{stream}/subject/{subject}/{source}', [StudentPastPaperController::class, 'showSubjectPapers'])->name('past_papers.subject.papers');
            Route::get('/past-papers/{paper}/start', [StudentPastPaperAttemptController::class, 'start'])->name('past_papers.start');
            Route::get('/past-papers/attempts/{attempt}', [StudentPastPaperAttemptController::class, 'show'])->name('past_papers.attempt.show');
            Route::get('/past-papers/attempts/{attempt}/meta', [StudentPastPaperAttemptController::class, 'meta'])->name('past_papers.attempt.meta');
            Route::get('/past-papers/attempts/{attempt}/question', [StudentPastPaperAttemptController::class, 'question'])->name('past_papers.attempt.question');
            Route::post('/past-papers/attempts/{attempt}/answers', [StudentPastPaperAttemptController::class, 'saveAnswer'])->name('past_papers.attempt.save_answer');
            Route::post('/past-papers/attempts/{attempt}/submit', [StudentPastPaperAttemptController::class, 'submit'])->name('past_papers.attempt.submit');
            Route::get('/past-papers/attempts/{attempt}/review', [StudentPastPaperAttemptController::class, 'review'])->name('past_papers.attempt.review');
            Route::get('/past-papers/attempts/{attempt}/result', [StudentPastPaperAttemptController::class, 'result'])->name('past_papers.attempt.result');

            Route::get('/exams', [StudentExamController::class, 'index'])->name('exams.index');
            Route::get('/results', [StudentExamController::class, 'resultsIndex'])->name('results.index');

            Route::post('/exams/{exam}/verify-code', [StudentExamController::class, 'verifyCode'])
                ->name('exams.verifyCode');
            Route::get('/exams/{exam}/start', [StudentExamController::class, 'start'])->name('exams.start');

            Route::post('/exams/{exam}/attempts/{attempt}/answer', [StudentExamController::class, 'saveAnswer'])
                ->name('exams.answer.save');

            Route::get('/attempts/{attempt}/meta', [StudentAttemptController::class, 'meta'])
                ->name('attempts.meta');

            Route::get('/attempts/{attempt}/question', [StudentAttemptController::class, 'question'])
                ->name('attempts.question');

            Route::post('/attempts/{attempt}/answers', [StudentAttemptController::class, 'answers'])
                ->name('attempts.answers');

            Route::post('/attempts/{attempt}/submit', [StudentAttemptController::class, 'submit'])
                ->name('attempts.submit');

            Route::get('/exams/{exam}/attempts/{attempt}/review', [StudentExamController::class, 'review'])
                ->name('exams.review');

            Route::get('/exams/{exam}/attempts/{attempt}', [StudentExamController::class, 'attempt'])
                ->name('exams.attempt');

            Route::post('/exams/{exam}/attempts/{attempt}/submit', [StudentExamController::class, 'submit'])
                ->name('exams.submit');

            Route::get('/exams/{exam}/attempts/{attempt}/result', [StudentExamController::class, 'result'])
                ->name('exams.result');

            // PDF Exam Paper
            Route::get('/exams/{exam}/attempts/{attempt}/paper', [StudentExamController::class, 'paper'])
                ->name('exams.paper');
        });

});
