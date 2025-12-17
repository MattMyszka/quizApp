@extends('layouts.mainLayout')

@section('content')
<div class="max-w-4xl mx-auto mt-16 p-8 bg-white shadow-2xl rounded-3xl border border-gray-100">
    
    @auth
        {{-- Dane profilu --}}
        <div class="text-center mb-10">
            <div class="w-24 h-24 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Witaj, {{ Auth::user()->name }}!</h2>
            <p class="text-gray-500 mb-6">{{ Auth::user()->email }}</p>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-50 text-red-600 rounded-xl font-bold hover:bg-red-100 transition-colors">
                    Wyloguj się
                </button>
            </form>
        </div>

        {{-- Sekcja Zarządzania Quizami --}}
        <div class="mt-12 border-t pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-center mb-8 gap-4">
                <h3 class="text-2xl font-bold text-gray-800">Twoje Quizy</h3>
                <a href="{{ route('quiz.create') }}" class="flex items-center px-6 py-3 bg-green-500 text-white rounded-xl font-bold hover:bg-green-600 shadow-lg">
                    + Stwórz Nowy Quiz
                </a>
            </div>

            <div class="grid gap-4">
                @php
                    $userQuizzes = (Auth::id() == 1) 
                        ? \App\Models\Quiz::all() 
                        : Auth::user()->quizzes;
                @endphp

                @forelse($userQuizzes as $quiz)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-100 shadow-sm">
                        <div class="flex items-center gap-4">
                            <img src="{{ $quiz->image_url ?? asset('images/default.png') }}" class="w-14 h-14 rounded-xl object-cover">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $quiz->title }}</h4>
                                <p class="text-xs text-gray-400">Pytań: {{ $quiz->num_questions }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('quiz.edit', $quiz->id) }}" class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2"/></svg>
                            </a>
                            <form action="{{ route('quiz.delete', $quiz->id) }}" method="POST" onsubmit="return confirm('Czy na pewno chcesz usunąć ten quiz?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-400 italic py-4">Nie masz jeszcze żadnych utworzonych quizów.</p>
                @endforelse
            </div>
        </div>

    @else
        {{-- Widok dla GOŚCIA (Logowanie/Rejestracja) --}}
        <div id="authContainer">
            <div class="flex mb-8 bg-gray-100 p-1 rounded-xl">
                <button onclick="toggleAuth('login')" id="loginTab" class="flex-1 py-2 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600">Logowanie</button>
                <button onclick="toggleAuth('register')" id="registerTab" class="flex-1 py-2 rounded-lg font-bold text-sm text-gray-500">Rejestracja</button>
            </div>

            <form id="loginForm" action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500">
                <input type="password" name="password" placeholder="Hasło" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all">Zaloguj się</button>
            </form>

            <form id="registerForm" action="{{ route('register') }}" method="POST" class="space-y-4 hidden">
                @csrf
                <input type="text" name="name" placeholder="Imię i nazwisko" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500">
                <input type="email" name="email" placeholder="Email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500">
                <input type="password" name="password" placeholder="Hasło" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500">
                <input type="password" name="password_confirmation" placeholder="Powtórz hasło" required class="w-full px-4 py-3 rounded-xl border border-gray-200 outline-none focus:ring-2 focus:ring-green-500">
                <button type="submit" class="w-full py-4 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 transition-all">Stwórz konto</button>
            </form>

            @if ($errors->any())
                <div class="mt-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm italic">
                    @foreach ($errors->all() as $error) <p>{{ $error }}</p> @endforeach
                </div>
            @endif
        </div>
    @endauth
</div>

<script>
    function toggleAuth(type) {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const loginTab = document.getElementById('loginTab');
        const registerTab = document.getElementById('registerTab');

        if (type === 'login') {
            loginForm.classList.remove('hidden');
            registerForm.classList.add('hidden');
            loginTab.className = "flex-1 py-2 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600";
            registerTab.className = "flex-1 py-2 rounded-lg font-bold text-sm text-gray-500";
        } else {
            loginForm.classList.add('hidden');
            registerForm.classList.remove('hidden');
            registerTab.className = "flex-1 py-2 rounded-lg font-bold text-sm bg-white shadow-sm text-blue-600";
            loginTab.className = "flex-1 py-2 rounded-lg font-bold text-sm text-gray-500";
        }
    }
</script>
@endsection