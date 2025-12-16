<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\DB;

class QuizService
{
    public function createQuiz(string $title, array $questionsData, ?string $imageUrl = null, ?int $userId = null): Quiz
    {
        return DB::transaction(function () use ($title, $questionsData, $imageUrl, $userId) {
            $quiz = Quiz::create([
                'title' => $title,
                'image_url' => $imageUrl,
                'num_questions' => count($questionsData),
                'user_id' => $userId
            ]);

            foreach ($questionsData as $qData) {
                $question = $quiz->questions()->create([
                    'question_text' => $qData['text'], 
                ]);

                foreach ($qData['answers'] as $aData) {
                    $question->answers()->create([
                        'answer_text' => $aData['answer_text'], 
                        'is_correct' => isset($aData['is_correct']),
                    ]);
                }
            }
            return $quiz->load('questions.answers');
        });
    }

    public function updateQuiz(int $id, array $data, array $questionsData, ?string $imageUrl = null): Quiz
    {
        return DB::transaction(function () use ($id, $data, $questionsData, $imageUrl) {
            $quiz = Quiz::findOrFail($id);
            $quiz->title = $data['title'];
            if ($imageUrl) $quiz->image_url = $imageUrl;
            $quiz->num_questions = count($questionsData);
            $quiz->save();

            $quiz->questions()->delete();

            foreach ($questionsData as $qData) {
                $question = $quiz->questions()->create([
                    'question_text' => $qData['text'],
                ]);

                foreach ($qData['answers'] as $aData) {
                    $question->answers()->create([
                        'answer_text' => $aData['answer_text'],
                        'is_correct' => isset($aData['is_correct']),
                    ]);
                }
            }
            return $quiz;
        });
    }

    public function getQuiz(int $quizId): Quiz
    {
        return Quiz::with('questions.answers')->findOrFail($quizId);
    }

    public function getAllQuizzes()
    {
        return Quiz::all();
    }
}