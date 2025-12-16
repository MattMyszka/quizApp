@extends('layouts.mainLayout')

@section('content')
<div class="quiz-container">
    <h1 class="quiz-header">Edytuj Quiz: {{ $quiz->title }}</h1>

    <form action="{{ route('quiz.update', $quiz->id) }}" method="POST" enctype="multipart/form-data" id="quizForm">
        @csrf
        @method('PUT')

        <div class="space-y-4 mb-10">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nazwa Quizu</label>
                <input type="text" name="title" id="quizTitle" value="{{ $quiz->title }}" required 
                    class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Obrazek Quizu</label>
                <div class="image-dropzone cursor-pointer" onclick="document.getElementById('image').click()" id="dropzone">
                    <div class="space-y-2 text-center">
                        {{-- Kontener podglądu --}}
                        <div id="preview-container">
                            @if($quiz->image_url)
                                <img id="image-preview" src="{{ asset($quiz->image_url) }}" 
                                    class="mx-auto h-32 w-32 object-cover rounded-lg shadow-md mb-2 border">
                                <p id="file-status" class="font-medium text-gray-500 text-sm">Aktualne zdjęcie</p>
                            @else
                                <svg class="mx-auto h-12 w-12 text-gray-400" id="upload-icon" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p id="file-status" class="font-medium text-blue-600 text-sm">Kliknij, aby dodać zdjęcie</p>
                            @endif
                            <img id="new-image-preview" src="#" alt="Nowy podgląd" class="hidden mx-auto h-32 w-32 object-cover rounded-lg shadow-md mb-2 border-2 border-green-400">
                        </div>

                        <div class="flex text-sm text-gray-600 justify-center">
                            <input id="image" name="image" type="file" class="hidden" accept="image/*">
                        </div>
                        <p id="file-info" class="text-xs text-gray-400">Zostaw puste, aby zachować obecne zdjęcie</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="questions-container" class="space-y-6">
            {{-- JS wstrzyknie dane --}}
        </div>

        <div class="mt-10 flex justify-center">
            <button type="button" id="add-question-btn" class="flex items-center px-8 py-4 bg-green-500 text-white rounded-full font-bold hover:bg-green-600 transition-all shadow-lg">
                + Dodaj Nowe Pytanie
            </button>
        </div>

        <div class="mt-16 flex justify-between items-center border-t pt-8">
            <a href="{{ route('account') }}" class="px-10 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold uppercase">Anuluj</a>
            <button type="submit" id="submit-quiz" class="px-10 py-3 bg-blue-600 text-white rounded-lg font-bold uppercase shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                Zapisz Zmiany
            </button>
        </div>
    </form>
</div>

<div id="quiz-data" data-questions='@json($quiz->questions->load("answers"))'></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('questions-container');
    const addQBtn = document.getElementById('add-question-btn');
    const submitBtn = document.getElementById('submit-quiz');
    const quizDataElement = document.getElementById('quiz-data');
    const fileInput = document.getElementById('image');
    const fileStatus = document.getElementById('file-status');
    const fileInfo = document.getElementById('file-info');
    const oldPreview = document.getElementById('image-preview'); 
    const newPreview = document.getElementById('new-image-preview');
    const uploadIcon = document.getElementById('upload-icon');
    
    let questionIndex = 0;
    const existingData = JSON.parse(quizDataElement.dataset.questions);

    function addQuestion(data = null) {
        const qId = questionIndex++;
        const qDiv = document.createElement('div');
        qDiv.className = 'question-card';
        
        const qText = data ? data.question_text : '';

        qDiv.innerHTML = `
            <div class="flex justify-between items-center mb-6">
                <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-bold uppercase tracking-widest">Pytanie #${questionIndex}</span>
                <button type="button" class="remove-question text-red-400 hover:text-red-600 font-semibold text-sm uppercase">Usuń</button>
            </div>
            <div class="mb-8">
                <input type="text" name="questions[${qId}][text]" value="${qText}" required placeholder="Treść pytania..."
                    class="block w-full px-5 py-3 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-blue-400 question-input">
            </div>
            <div class="answers-area space-y-4" id="ans-container-${qId}"></div>
            <button type="button" class="add-answer-btn mt-4 text-blue-600 font-bold text-sm">+ Dodaj Odpowiedź</button>
        `;

        container.appendChild(qDiv);
        const ansContainer = document.getElementById(`ans-container-${qId}`);

        if (data && data.answers) {
            data.answers.forEach(ans => addAnswer(qId, ans));
        } else {
            addAnswer(qId); addAnswer(qId);
        }

        qDiv.querySelector('.remove-question').addEventListener('click', () => { qDiv.remove(); validate(); });
        qDiv.querySelector('.add-answer-btn').addEventListener('click', () => addAnswer(qId));
        qDiv.querySelector('.question-input').addEventListener('input', validate);
        validate();
    }

    function addAnswer(qId, data = null) 
    {
        const ansContainer = document.getElementById(`ans-container-${qId}`);
        const aId = data && data.id ? data.id : (Date.now() + Math.random());
        const aText = data ? data.answer_text : '';
        const isCorrect = data ? (data.is_correct == 1 || data.is_correct === true) : false;

        const aDiv = document.createElement('div');
        aDiv.className = isCorrect ? 'answer-row active' : 'answer-row';
        
        aDiv.innerHTML = `
            <input type="text" name="questions[${qId}][answers][${aId}][answer_text]" value="${aText}" required placeholder="Odpowiedź..."
                class="answer-input flex-1 border-none focus:ring-0 text-sm font-medium bg-transparent">
            <label class="relative inline-flex items-center cursor-pointer">
                <input type="checkbox" name="questions[${qId}][answers][${aId}][is_correct]" value="1" 
                    ${isCorrect ? 'checked' : ''} class="hidden correct-check">
                <div class="toggle-pill ${isCorrect ? 'true' : 'false'}">${isCorrect ? 'PRAWDA' : 'FAŁSZ'}</div>
            </label>
            <button type="button" class="remove-ans text-gray-300 hover:text-red-500 text-xl px-2">×</button>
        `;

        ansContainer.appendChild(aDiv);

        const checkbox = aDiv.querySelector('.correct-check');
        const pill = aDiv.querySelector('.toggle-pill');

        checkbox.addEventListener('change', function() {
            if(this.checked) {
                pill.innerText = 'PRAWDA';
                pill.className = 'toggle-pill true';
                aDiv.classList.add('active');
            } else {
                pill.innerText = 'FAŁSZ';
                pill.className = 'toggle-pill false';
                aDiv.classList.remove('active');
            }
            validate();
        });

        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if(oldPreview) oldPreview.classList.add('hidden');
                    if(uploadIcon) uploadIcon.classList.add('hidden');
                    
                    newPreview.src = e.target.result;
                    newPreview.classList.remove('hidden');
                    
                    fileStatus.textContent = "Wybrano nowy: " + file.name;
                    fileStatus.className = "font-medium text-green-600 text-sm";
                    
                    const fileSize = (file.size / 1024).toFixed(1);
                    fileInfo.textContent = `Nowy rozmiar: ${fileSize} KB (zastąpi poprzedni)`;
                }
                reader.readAsDataURL(file);
            } 
            else {
                if(oldPreview) {
                    oldPreview.classList.remove('hidden');
                    newPreview.classList.add('hidden');
                    fileStatus.textContent = "Aktualne zdjęcie";
                    fileStatus.className = "font-medium text-gray-500 text-sm";
                    fileInfo.textContent = "Zostaw puste, aby zachować obecne zdjęcie";
                }
            }
        });

        aDiv.querySelector('.remove-ans').addEventListener('click', () => {
            if(ansContainer.querySelectorAll('.answer-row').length > 1) { 
                aDiv.remove(); 
                validate(); 
            }
        });

        aDiv.querySelector('.answer-input').addEventListener('input', validate);
    }

    function validate() {
        const questions = container.querySelectorAll('.question-card');
        const quizTitle = document.getElementById('quizTitle').value.trim();
        let isValid = questions.length > 0 && quizTitle.length > 0;

        questions.forEach(q => {
            const hasCorrect = q.querySelectorAll('.correct-check:checked').length > 0;
            const qInput = q.querySelector('.question-input').value.trim();
            const ansInputs = Array.from(q.querySelectorAll('.answer-input')).every(i => i.value.trim() !== "");
            if(qInput === "" || !ansInputs || !hasCorrect) isValid = false;
        });
        submitBtn.disabled = !isValid;
    }

    if (existingData.length > 0) existingData.forEach(q => addQuestion(q));
    else addQuestion();

    document.getElementById('quizTitle').addEventListener('input', validate);
    addQBtn.addEventListener('click', () => addQuestion());
    validate();
});
</script>
@endsection