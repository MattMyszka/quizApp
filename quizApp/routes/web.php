<?php

use App\Http\Controllers\QuizController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [QuizController::class, 'index']) 
    ->name('mainPage');

Route::prefix('quiz')->group(function () {
    Route::get('/create', function () {return view('newQuiz');})->name('quiz.create')->middleware('auth');
    Route::get('/{id}', [QuizController::class, 'show'])->name('quiz');
    Route::get('/{id}/edit', [QuizController::class, 'edit'])->name('quiz.edit')->middleware('auth');
    Route::put('/{id}', [QuizController::class, 'update'])->name('quiz.update')->middleware('auth');
    Route::delete('/{id}', [QuizController::class, 'destroy'])->name('quiz.delete')->middleware('auth');
});

Route::post('/newQuiz', [QuizController::class, 'store'])->name('quiz.store');

Route::get('/account', [AuthController::class, 'showAccount'])->name('account');

Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');