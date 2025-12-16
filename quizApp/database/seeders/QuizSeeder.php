<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\Answer;
use Database\Factories\AnswerFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Faker\Factory as Faker;
use Carbon\Carbon;

class QuizSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $imageFileName = 'seeder_image.png';
        $imagePath = 'images/quizzes/' . $imageFileName;
        $imageUrl = null;

        $ileQuizów = (int)5;

        if (Storage::disk('public')->exists($imagePath)) {
            $imageUrl = Storage::url($imagePath);
        } else {
            $this->command->warn("Ostrzeżenie: Plik obrazka nie znaleziony w: " . $imagePath);
        }

        //czyszczenie tabeli dla seedera.
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Answer::truncate(); 
        Question::truncate();
        Quiz::truncate(); 
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        $this->command->info('Wyczyszczono tabele quizów, pytań i odpowiedzi.');

        //seeding
        DB::transaction(function () use ($imageUrl, $faker, $ileQuizów) { 
            
            Quiz::factory($ileQuizów)
                ->create()
                ->each(function (Quiz $quiz) use ($imageUrl) {
                    
                    $quiz->title = "Przykładowy quiz #$quiz->id";

                    if ($imageUrl) {
                        $quiz->image_url = $imageUrl;
                        $quiz->save();
                    }

                    $numQuestions = rand(2, 20);
                    $questions = Question::factory($numQuestions)->create([
                        'quiz_id' => $quiz->id,
                    ]);

                    $quiz->num_questions = $numQuestions;
                    $quiz->save();

                    $questions->each(function (Question $question) { 
                        
                        $numAnswers = rand(2, 10);
                        $answers = AnswerFactory::new()->count($numAnswers)->make([
                            'question_id' => $question->id 
                        ]);
                        
                        $answersData = $answers->map(function ($answer, $key) use ($numAnswers) {
                            $data = $answer->toArray();
                            
                            if ($key === 0) { 
                                $data['is_correct'] = true;
                            }
                            return $data;
                        })->toArray();
                        
                        $question->answers()->createMany($answersData);
                    });
                });
            
            $this->command->info('Pomyślnie wygenerowano losowych quizów:'. $ileQuizów);
        });
    }
}