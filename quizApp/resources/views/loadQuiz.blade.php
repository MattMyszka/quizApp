<!DOCTYPE html>
<html lang="pl">

@php
use App\Models\Quiz;
$quiz = Quiz::with('questions.answers')->find($quizID);
$quizDataJson = json_encode($quiz->questions);
@endphp


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quiz->title }}</title>
    @vite('resources/css/app.css')
</head>

<body>
<div id="quiz-app">
    
<!-- Tytuł quizu -->
    <div class="quiz-title">
        {{ $quiz->title }}
    </div>

    <!-- Kontener na pytania -->
    <div id="dynamic-question-container">
        <div class="question-box">
            <div class="question-image" style="display: none;">
                <img src="" alt="Obrazek pytania">
            </div>
            
            <div class="question-text"></div>

            <div class="answers-grid">
                </div>
        </div>
    </div>

    <!-- Pasek postępu -->
    <div class="progress-container">
        <div class="progress-bar" style="width: 0%;"></div>
    </div>

    <!-- Wynik końcowy -->
    <<div class="result-overlay">
    <div class="result-box">
        <div class="result-title">Twój wynik</div>
        <div class="result-text" id="result-text"></div>
        
        <button id="main-page-button" class="btn-main-page">
            Przejdź na stronę główną
        </button>
        
    </div>
</div>
</div>

<script>
    const quizData = JSON.parse('{!! $quizDataJson !!}');

    let currentIndex = 0;
    let correctCount = 0;
    const total = quizData.length;

    function loadQuestion() {
        const q = quizData[currentIndex];

        // Obrazek
        const imgContainer = document.querySelector('.question-image');
        const imgEl = imgContainer?.querySelector('img');
        if (q.image_url && imgEl) {
            imgContainer.style.display = 'block';
            imgEl.src = q.image_url;
        } else if(imgContainer) {
            imgContainer.style.display = 'none';
        }

        // Tekst
        document.querySelector('.question-text').innerText = q.question_text;

        // Odpowiedzi
        const answersGrid = document.querySelector('.answers-grid');
        answersGrid.innerHTML = '';

        q.answers.forEach(ans => {
            const div = document.createElement('div');
            div.className = "answer-box";
            div.innerText = ans.answer_text;

            div.addEventListener('click', (event) => {
                document.querySelectorAll('.answer-box').forEach(box => {
                    box.style.pointerEvents = 'none';
                });

                if (ans.is_correct) {
                    correctCount++;
                    event.target.classList.add('correct');
                } else {
                    event.target.classList.add('incorrect');
                    
                    q.answers.forEach((correctAns, index) => {
                        if(correctAns.is_correct) {
                            document.querySelector(`.answers-grid`).children[index].classList.add('correct');
                        }
                    });
                }

                setTimeout(() => {
                    nextQuestion();
                }, 1000); 
            });

        answersGrid.appendChild(div);
        });

        updateProgress();
    }

    function updateProgress() {
        const progress = (currentIndex / total) * 100;
        document.querySelector('.progress-bar').style.width = progress + '%';
    }

    function nextQuestion() {
        currentIndex++;
        if (currentIndex >= total) {
            finishQuiz();
        } else {
            loadQuestion();
        }
    }

    function finishQuiz() {
        document.getElementById('quiz-app').scrollIntoView();
        document.querySelector('.result-overlay').style.display = 'flex';
        document.getElementById('result-text').innerText = `Poprawnych odpowiedzi: ${correctCount} / ${total}`;
        document.querySelector('.progress-bar').style.width = '100%';

        const mainButton = document.getElementById('main-page-button');
        if (mainButton) {
            mainButton.addEventListener('click', () => {
                window.location.href = '/'; 
            });
        }
    }

    // Start
    loadQuestion();
</script>

</body>
</html>