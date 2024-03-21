<?php

namespace App\Http\Controllers;

use App\Models\SendEmail;
use Illuminate\Http\Request;

class SendEmailController extends Controller
{
    public function update (Request $request, SendEmail $send_email) {
        if ($request->user()->cannot('update', $send_email)) abort(403);
        $send_email->update([
            'email' => $request->email,
            'enabled' => $request->input('enabled', false)
        ]);
        return back()->with('toast', ['success', '送信先メールアドレスを変更しました']);
    }

    public function destroy (Request $request, SendEmail $send_email) {
        if ($request->user()->cannot('forceDelete', $send_email)) abort(403);
        $send_email->delete();
        return back()->with('toast', ['info', '送信先メールアドレスを削除しました']);
    }
}
