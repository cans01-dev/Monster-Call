<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function show (User $user) {
        return view('pages.user', ['user' => $user]);
    }

    public function update (Request $request, User $user) {
        if ($request->user()->cannot('update', $user)) abort(403);
        $user->update([
            'email' => $request->email,
            'name' => $request->name
        ]);
        return back()->with('toast', ['success', 'ユーザー情報を変更しました']);
    }

    public function store_send_email (Request $request, User $user) {
        $user->send_emails()->create([
            'email' => $request->email,
            'enabled' => $request->input('enabled', false)
        ]);
        return back()->with('toast', ['success', '送信先メールアドレスを追加しました']);
    }

    public function update_password (Request $request, User $user) {
        if (!password_verify($request->old_password, $user->password)) {
            return back()->with('toast', ['danger', '現在のパスワードが異なります']);
        }
        if ($request->new_password !== $request->new_password_confirm) {
            return back()->with('toast', ['danger', '新しいパスワードの再入力が正しくありません']);
        }
        $user->update([
            'password' => $request->new_password
        ]);
        return back()->with('toast', ['success', 'パスワードを変更しました']);
    }

    public function store_survey (Request $request, User $user) {
        if ($user->survey()->exists()) {
            return back()->with('toast', ['danger', '現在のユーザーでは一つ以上のアンケートを作成することはできません']);
        }
        $file_uniqid = config('app.default_greeting_file_name');
        Storage::copy('samples/defaultGreeting.wav', "users/{$user->id}/{$file_uniqid}.wav");

        $survey = $user->survey()->create([
            'title' => $request->title,
            'note' => $request->note,
            'voice_name' => $request->voice_name
        ]);
        return redirect("/surveys/{$survey->id}")->with('toast', ['success', 'アンケートを新規作成しました']);
    }
}
