<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuizApp</title>

     @vite('resources/css/app.css')
</head>

<body class="bg-gray-100">

    {{-- Pasek główny --}}
    <nav class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        
        {{-- Lewa strona – pusta, żeby logo było idealnie na środku --}}
        <div class="w-1/3"></div>

        {{-- Środek – "QuizApp" jako przycisk/kliknięcie --}}
        <div class="w-1/3 flex justify-center">
            <a href="{{ route('mainPage') }}" class="text-2xl font-bold text-blue-600 hover:text-blue-800">
                QuizApp
            </a>
        </div>

        {{-- Prawa strona – ikona użytkownika --}}
        <div class="w-1/3 flex justify-end">
            <a href="{{ route('account') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-8 h-8 text-gray-700 hover:text-black">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 
                           20.25a8.25 8.25 0 1115 0v.75H4.5v-.75z" />
                </svg>
            </a>
        </div>
    </nav>

    {{-- Treść podstrony --}}
    <main class="max-w-5xl mx-auto mt-8 px-4">
        @yield('content')
    </main>

</body>
</html>
