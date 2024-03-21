<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    public function show (Request $request, Survey $survey) {
        return view('survey', ['survey' => $survey]);
    }

    public function update (Request $request, Survey $survey) {
        if ($request->user()->cannot('update', $survey)) abort(403);
        $survey->update([
            'title' => $request->title,
            'note' => $request->note,
            'voice_name' => $request->voice_name
        ]);
        return back()->with('toast', ['success', 'ユーザー情報を変更しました']);
    }

    public function destroy (Request $request, Survey $survey) {
        return view('survey', ['survey' => $survey]);
    }
}
