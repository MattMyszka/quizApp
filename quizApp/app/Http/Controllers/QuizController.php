<?php
namespace App\Http\Controllers;

use App\Services\QuizService;
use Illuminate\Http\Request;

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
        $quiz = $this->quizService->getQuiz($id);
        return view('quizDetail', compact('quiz'));
    }

    public function store(Request $request)
    {
        $quiz = $this->quizService->createQuiz($request->title, $request->questions);
        return redirect()->route('quizzes.show', $quiz->id);
    }
}
