<?php
use App\Models\Quiz;


    $quiz = Quiz::with('questions.answers')->find($quizID);

    if($quiz != null)
    {
        foreach ($quiz->questions as $question) {
            echo $question->question_text . "\n";
            foreach ($question->answers as $answer) {
                echo "- " . $answer->answer_text . ($answer->is_correct ? " (poprawna)" : "") . "\n";
            }
        }
    }
    else
    {
        echo "Ups! Nie znaleźlismy tego quizu!";
    }