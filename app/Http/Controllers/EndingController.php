<?php

namespace App\Http\Controllers;

use App\Libs\MyUtil;
use App\Models\Ending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EndingController extends Controller
{
    public function update (Request $request, Ending $ending) {
        $file_name = uniqid() . '.wav';
        $file_contents = MyUtil::text_to_speech($request->text, $ending->survey->voice_name);
        Storage::put("users/{$ending->survey->user->id}/{$file_name}", $file_contents);

        $ending()->update([
            'title' => $request->title,
            'text' => $request->text,
            'voice_file' => $file_name
        ]);

        return back()->with('toast', ['success', 'エンディングを変更しました']);
    }
}
