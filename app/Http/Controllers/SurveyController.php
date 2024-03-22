<?php

namespace App\Http\Controllers;

use App\Libs\MyUtil;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SurveyController extends Controller
{
    public function show (Request $request, Survey $survey) {
        return view('pages.survey', ['survey' => $survey]);
    }

    public function update (Request $request, Survey $survey) {
        if ($request->user()->cannot('update', $survey)) abort(403);
        $survey->update([
            'title' => $request->title,
            'note' => $request->note,
            'voice_name' => $request->voice_name,
            'success_ending_id' => $request->success_ending_id
        ]);
        return back()->with('toast', ['success', 'ユーザー情報を変更しました']);
    }

    public function update_greeting (Request $request, Survey $survey) {
        if ($request->user()->cannot('update', $survey)) abort(403);

        $file_name = uniqid() . '.wav';
        $file_contents = MyUtil::text_to_speech($request->greeting, $survey->voice_name);
        Storage::put("users/{$survey->user->id}/{$file_name}", $file_contents);
      
        $survey->update([
            'greeting' => $request->greeting,
            'greeting_voice_file' => $file_name
        ]);
        return back()->with('toast', ['success', 'グリーティングを変更しました']);
    }

    public function store_ending (Request $request, Survey $survey) {
        $file_name = uniqid() . '.wav';
        $file_contents = MyUtil::text_to_speech($request->text, $survey->voice_name);
        Storage::put("users/{$survey->user->id}/{$file_name}", $file_contents);

        $survey->endings()->create([
            'title' => $request->title,
            'text' => $request->text,
            'voice_file' => $file_name
        ]);
        return back()->with('toast', "エンディングを作成しました");
    }

    public function store_faq (Request $request, Survey $survey) {
        $file_name = uniqid() . '.wav';
        $file_contents = MyUtil::text_to_speech($request->text, $survey->voice_name);
        Storage::put("users/{$survey->user->id}/{$file_name}", $file_contents);

        $order_num = $survey->faqs()->max('order_num') + 1;

        $faq = $survey->faqs()->create([
            'title' => $request->title,
            'text' => $request->text,
            'order_num' => $order_num,
            'voice_file' => $file_name,
        ]);

        $faq->options()->create([
            'title' => '聞き直し',
            'dial' => 0,
            'next_faq_id' => $faq->id
        ]);

        return redirect("/faqs/{$faq->id}")->with('toast', ['success', '質問を新規作成しました']);
    }

    public function destroy (Request $request, Survey $survey) {
        return view('pages.survey', ['survey' => $survey]);
    }
}
