<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            //Quiz
            $quiz = Quiz::create([
                'title' => 'Przykładowy quiz 3',
                'num_questions' => 3
            ]);

            // Pytanie 1
            $question1 = $quiz->questions()->create([
                'question_text' => 'Gdzie mieszka Pani Kasia'
            ]);
            $question1->answers()->createMany([
                ['answer_text' => 'Dąbrowa', 'is_correct' => true],
                ['answer_text' => 'Poznań', 'is_correct' => false],
                ['answer_text' => 'Bobowicko', 'is_correct' => true],
                ['answer_text' => 'Zielona Góra', 'is_correct' => false]
            ]);

            // Pytanie 2
            $question2 = $quiz->questions()->create([
                'question_text' => 'Jaki alkohol zrobili Pani Kasia z Panem Maciejem'
            ]);
            $question2->answers()->createMany([
                ['answer_text' => 'dobry', 'is_correct' => true],
                ['answer_text' => 'mirabelkowy', 'is_correct' => true],
            ]);

            //Pytanie 3
            $question3 = $quiz->questions()->create([
                'question_text' => 'Ile tatuaży ma Pani Kasia'
            ]);
            $question3->answers()->createMany([
                ['answer_text' => 'dużo', 'is_correct' => true],
                ['answer_text' => 'w chuj', 'is_correct' => true],
                ['answer_text' => 'kto wie', 'is_correct' => true],
                ['answer_text' => 'cztery', 'is_correct' => false],
            ]);
        });
    }
}
