<?php

namespace Database\Factories;

use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Question::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            // Losowy tekst pytania, kończący się znakiem zapytania
            'question_text' => $this->faker->sentence(rand(5, 10), true) . '?',
            
            // Definiowanie stanu domyślnego relacji (jeśli nie jest podany w seederze)
            'quiz_id' => Quiz::factory(), 
        ];
    }
}