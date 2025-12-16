<!DOCTYPE html>
<html lang="pl">

@extends('layouts.mainLayout')

@section('content')
<h1 class="text-3xl font-semibold mb-6">Dostępne Quizy</h1>

<div class="quiz-grid">
    @foreach ($quizzes as $quiz)
        
        <div class="quiz-card" data-quiz-id="{{ $quiz->id }}">
            
            {{-- Cały kontener jest klikalny (oprócz przycisku) --}}
            <a href="{{ route('quiz',$quiz->id) }}" class="block quiz-link">
                
                <div class="quiz-card-image-section">
                    @if ($quiz->image_url)
                        <img src="{{ $quiz->image_url }}" alt="Obrazek dla quizu {{ $quiz->title }}">
                    @else
                        {{-- Upewnij się, że ten span nie ma już klasy "p-4", która była z Tailwind --}}
                        <span>Obrazek z bazy (Placeholder)</span>
                    @endif
                </div>

                <div class="quiz-card-content">
                    {{-- Używamy klasy z ustaloną wysokością i elipsą --}}
                    <h2 class="quiz-title-fixed-height text-xl font-extrabold text-gray-800">
                        {{ $quiz->title }}
                    </h2>
                    
                    <p class="text-gray-500 text-sm">
                        Liczba pytań: {{ $quiz->num_questions }}
                    </p>
                </div>

                <div class="quiz-card-start-button js-start-button">
                    Rozpocznij
                </div>
            </a>
        </div>
    @endforeach
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const quizCards = document.querySelectorAll('.quiz-card');

    quizCards.forEach(card => {
        card.addEventListener('click', function(event) {
            
            const isStartButton = event.target.closest('.js-start-button');
            const quizDetails = card.querySelector('.quiz-details');

            if (isStartButton) {
                return;
            }

            event.preventDefault(); 
            event.stopPropagation(); 

            
            card.classList.toggle('is-expanded');
            
            if (quizDetails) {
                quizDetails.style.display = quizDetails.style.display === 'none' ? 'block' : 'none';
            }
            
        });
    });
});
</script>

@endsection