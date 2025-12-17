@extends('layouts.mainLayout')

@section('content')
<div class="quiz-container">
    <h1 class="quiz-header">Stwórz Nowy Quiz</h1>

    <form action="{{ route('quiz.store') }}" method="POST" enctype="multipart/form-data" id="quizForm">
        @csrf

        <div class="space-y-4 mb-10">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nazwa Quizu</label>
                <input type="text" name="title" id="quizTitle" required placeholder="Wpisz nazwę, np. Historia Świata"
                    class="mt-1 block w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 text-lg">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Obrazek Quizu</label>
                <div class="image-dropzone cursor-pointer" onclick="document.getElementById('image').click()" id="dropzone">
                    <div class="space-y-2 text-center" id="dropzone-content">
                        <svg class="mx-auto h-12 w-12 text-gray-400" id="upload-icon" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div id="preview-container" class="hidden">
                            <img id="image-preview" src="#" alt="Podgląd" class="mx-auto h-32 w-32 object-cover rounded-lg shadow-md mb-2">
                        </div>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <span id="file-status" class="font-medium text-blue-600">Kliknij, aby wgrać plik</span>
                            <input id="image" name="image" type="file" class="hidden" accept="image/*">
                        </div>
                        <p id="file-info" class="text-xs text-gray-500">PNG, JPG do 2MB</p>
                    </div>
                </div>
            </div>
        </div>

        <div id="questions-container" class="space-y-6">
            {{-- JS wstrzykuje .question-card --}}
        </div>

        <div class="mt-10 flex justify-center">
            <button type="button" id="add-question-btn" class="flex items-center px-8 py-4 bg-green-500 text-white rounded-full font-bold hover:bg-green-600 transition-all shadow-lg transform hover:scale-105">
                + Dodaj Kolejne Pytanie
            </button>
        </div>

        <div class="mt-16 flex justify-between items-center border-t pt-8">
            <a href="{{ route('mainPage') }}" class="px-10 py-3 bg-gray-200 text-gray-700 rounded-lg font-bold uppercase">Anuluj</a>
            <button type="submit" id="submit-quiz" disabled class="px-10 py-3 bg-blue-600 text-white rounded-lg font-bold uppercase shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                Potwierdź i Utwórz Quiz
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('questions-container');
        const addQBtn = document.getElementById('add-question-btn');
        const submitBtn = document.getElementById('submit-quiz');
        const quizTitleInput = document.getElementById('quizTitle');
        const fileInput = document.getElementById('image');
        const fileStatus = document.getElementById('file-status');
        const fileInfo = document.getElementById('file-info');
        const previewContainer = document.getElementById('preview-container');
        const imagePreview = document.getElementById('image-preview');
        const uploadIcon = document.getElementById('upload-icon');

        let questionIndex = 0;

        function addQuestion() {
            const qId = questionIndex++;
            const qDiv = document.createElement('div');
            qDiv.className = 'question-card';
            qDiv.dataset.index = qId;

            qDiv.innerHTML = `
                <div class="flex justify-between items-center mb-6">
                    <span class="bg-blue-100 text-blue-700 px-4 py-1 rounded-full text-sm font-bold uppercase tracking-widest">Pytanie #${qId + 1}</span>
                    <button type="button" class="remove-question text-red-400 hover:text-red-600 font-semibold text-sm uppercase">Usuń</button>
                </div>
                <div class="mb-8">
                    <input type="text" name="questions[${qId}][text]" required placeholder="Wpisz treść pytania..."
                        class="block w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-400 outline-none text-gray-800 font-medium question-input">
                </div>
                <div class="answers-area space-y-4" id="ans-container-${qId}">
                    </div>
                <div class="mt-6 pt-4 border-t flex justify-between items-center">
                    <button type="button" class="add-answer-btn text-blue-600 hover:text-blue-800 font-bold text-sm">+ Dodaj Odpowiedź</button>
                    <p class="text-[10px] text-gray-400 italic">Zaznacz poprawną odpowiedź</p>
                </div>
            `;

            container.appendChild(qDiv);

            addAnswer(qId);
            addAnswer(qId);

            qDiv.querySelector('.remove-question').addEventListener('click', () => {
                qDiv.remove();
                validate();
            });

            qDiv.querySelector('.add-answer-btn').addEventListener('click', () => {
                addAnswer(qId);
            });

            qDiv.querySelector('.question-input').addEventListener('input', validate);
            validate();
        }

        function addAnswer(qId) {
            const ansContainer = document.getElementById(`ans-container-${qId}`);
            const aId = Date.now() + Math.random(); // Unikalne ID dla pol formularza
            const aDiv = document.createElement('div');
            aDiv.className = 'answer-row';
            
            aDiv.innerHTML = `
                <input type="text" name="questions[${qId}][answers][${aId}][answer_text]" required placeholder="Treść odpowiedzi..."
                    class="answer-input flex-1 border-none focus:ring-0 text-sm font-medium bg-transparent">
                <div class="flex items-center gap-3">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="questions[${qId}][answers][${aId}][is_correct]" value="1" class="hidden correct-check">
                        <div class="toggle-pill false">Fałsz</div>
                    </label>
                    <button type="button" class="remove-ans text-gray-300 hover:text-red-500 text-xl px-2">×</button>
                </div>
            `;

            ansContainer.appendChild(aDiv);

            const checkbox = aDiv.querySelector('.correct-check');
            const pill = aDiv.querySelector('.toggle-pill');

            checkbox.addEventListener('change', function() {
                if (this.checked) {
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
                    fileStatus.textContent = "Wybrano: " + file.name;
                    fileStatus.className = "font-medium text-green-600";

                    const fileSize = (file.size / 1024).toFixed(1); // w KB
                    const sizeText = fileSize > 1024 
                        ? (fileSize / 1024).toFixed(2) + " MB" 
                        : fileSize + " KB";
                    fileInfo.textContent = `Rozmiar: ${sizeText}`;

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        previewContainer.classList.remove('hidden');
                        uploadIcon.classList.add('hidden'); 
                    }
                    reader.readAsDataURL(file);
                    
                } else {
                    fileStatus.textContent = "Kliknij, aby wgrać plik";
                    fileStatus.className = "font-medium text-blue-600";
                    fileInfo.textContent = "PNG, JPG do 2MB";
                    previewContainer.classList.add('hidden');
                    uploadIcon.classList.remove('hidden');
                }
            });

            aDiv.querySelector('.remove-ans').addEventListener('click', () => {
                if (ansContainer.querySelectorAll('.answer-row').length > 2) {
                    aDiv.remove();
                    validate();
                }
            });

            aDiv.querySelector('.answer-input').addEventListener('input', validate);
            validate();
        }

        function validate() {
            const questions = container.querySelectorAll('.question-card');
            const quizTitle = quizTitleInput.value.trim();
            let isValid = questions.length > 0 && quizTitle.length > 0;

            questions.forEach(q => {
                const hasCorrect = q.querySelectorAll('.correct-check:checked').length > 0;
                const qInput = q.querySelector('.question-input').value.trim();
                const ansInputs = Array.from(q.querySelectorAll('.answer-input')).every(i => i.value.trim() !== "");
                
                if (qInput === "" || !ansInputs || !hasCorrect) {
                    isValid = false;
                }
            });

            submitBtn.disabled = !isValid;
        }

        addQBtn.addEventListener('click', addQuestion);
        quizTitleInput.addEventListener('input', validate);

        addQuestion();
    });
</script>
@endsection