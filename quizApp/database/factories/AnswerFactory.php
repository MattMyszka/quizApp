<?php

namespace Database\Factories;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnswerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            // Losowy, krótki tekst odpowiedzi
            'answer_text' => $this->faker->sentence(rand(1, 3), true),
            
            // Domyślnie ustawiamy na false
            'is_correct' => false, 
            
            // Definiowanie stanu domyślnego relacji
            'question_id' => Question::factory(),
        ];
    }
    
    public function correct(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'is_correct' => true,
            ];
        });
    }
}