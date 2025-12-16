<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Services\QuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizService)
    {
        $this->quizService = $quizService;
    }


    public function index()
    {
        $quizzes = $this->quizService->getAllQuizzes();
        return view('mainPage', compact('quizzes'));
    }

    public function show($id)
    {
        return view('loadQuiz', ['quizID' => $id]);
    }

    public function edit($id)
    {
        $quiz = Quiz::with('questions.answers')->findOrFail($id);
        
        if (Auth::id() !== $quiz->user_id && Auth::id() !== 1) {
            abort(403, 'Nie masz uprawnień do edycji tego quizu.');
        }

        return view('editQuiz', compact('quiz'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048',
            'questions' => 'required|array|min:1',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images/quizzes', $fileName, 'public');
            $imageUrl = '/storage/images/quizzes/' . $fileName;
        }

        $this->quizService->createQuiz(
            $request->title, 
            $request->questions, 
            $imageUrl,
            Auth::id()
        );

        return redirect()->route('mainPage')->with('success', 'Quiz utworzony!');
    }

    public function update(Request $request, $id)
    {
        $quiz = Quiz::findOrFail($id);

        if (Auth::id() !== $quiz->user_id && Auth::id() !== 1) {
            abort(403);
        }

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('images/quizzes', $fileName, 'public');
            $imageUrl = '/storage/images/quizzes/' . $fileName;
        }

        $this->quizService->updateQuiz($id, $request->only('title'), $request->questions, $imageUrl);

        return redirect()->route('account')->with('success', 'Zaktualizowano!');
    }

    public function destroy($id)
    {
        $quiz = Quiz::findOrFail($id);
        if (Auth::id() == $quiz->user_id || Auth::id() == 1) {
            $quiz->delete();
            return back()->with('success', 'Usunięto quiz.');
        }
        abort(403);
    }
}