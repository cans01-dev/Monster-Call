<?php

namespace App\Http\Controllers;

use App\Libs\MyUtil;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    public function show (Faq $faq) {
        return view('pages.faq', ['faq' => $faq]);
    }

    public function update (Request $request, Faq $faq) {
        if ($request->user()->cannot('update', $faq)) abort(403);

        $file_name = uniqid() . '.wav';
        $file_contents = MyUtil::text_to_speech($request->text, $faq->survey->voice_name);
        Storage::put("users/{$faq->survey->user->id}/{$file_name}", $file_contents);
      
        $faq->update([
            'title' => $request->title,
            'text' => $request->text,
            'voice_file' => $file_name
        ]);
        return back()->with('toast', ['success', '質問の設定を変更しました']);
    }

}
