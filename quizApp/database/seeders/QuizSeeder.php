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
                'title' => 'Przykładowy quiz 2',
                'num_questions' => 2
            ]);

            // Pytanie 1
            $question1 = $quiz->questions()->create([
                'question_text' => 'Gdzie mieszka Pan Maciej'
            ]);
            $question1->answers()->createMany([
                ['answer_text' => 'Dąbrowa', 'is_correct' => true],
                ['answer_text' => 'Poznań', 'is_correct' => false],
                ['answer_text' => 'Bobowicko', 'is_correct' => false],
            ]);

            // Pytanie 2
            $question2 = $quiz->questions()->create([
                'question_text' => 'Jakie Pan Maciej ma wykształcenie'
            ]);
            $question2->answers()->createMany([
                ['answer_text' => 'średnie', 'is_correct' => true],
                ['answer_text' => 'wyższe', 'is_correct' => false],
                ['answer_text' => 'doktorat', 'is_correct' => false],
            ]);
        });
    }
}
