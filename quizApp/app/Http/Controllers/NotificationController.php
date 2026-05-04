<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{
    public function sendScorePush(Request $request)
    {
        $topic = 'quizapp'; 
        
        $response = Http::withoutVerifying()->withHeaders([
            'Title' => "Wynik: " . ($request->input('title') ?? 'Quiz'),
            'Priority' => 'high',
            'Tags' => 'star-struck,tada'
        ])->post("https://ntfy.sh/{$topic}", 
            "Zdobyto " . $request->input('score') . " na " . $request->input('total') . " punktow!"
        );

        return response()->json(['status' => 'success']);
    }
}