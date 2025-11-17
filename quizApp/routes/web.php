<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('mainPage');
});

Route::get('/quiz/{id}', function ($quizId) {
    return view('loadQuiz')
    ->with('quizID',$quizId);
});

Route::get('/newQuiz', function(){
    return view('newQuiz');
});