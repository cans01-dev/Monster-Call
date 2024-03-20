<?php

use App\Models\SendEmail;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    if (Auth::check()) return redirect('home');
    return view('login');
})->name('login');

Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended('home')->with('toast', ['success', 'ログインしました']);
    }

    return back()->with('toast', ['danger', 'メールアドレスもしくはパスワードが異なります'])->onlyInput('email');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect('/home');
    });

    Route::get('/home', function () {
        return view('home');
    })->name('home');
    
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/login')->with('toast', ['info', 'ログアウトしました']);
    });

    Route::prefix('/users/{user}')->group(function () {
        Route::get('/', function (User $user) {
            return view('user', ['user' => $user]);
        });
    
        Route::post('/send_emails', function (Request $request, User $user) {
            $user->send_emails()->create([
                'email' => $request->email,
                'enabled' => $request->input('enabled', false)
            ]);
            return back()->with('toast', ['success', '送信先メールアドレスを追加しました']);
        });

        Route::put('/password', function (Request $request, User $user) {
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
        });

        Route::post('/surveys', function (Request $request, User $user) {
            if ($user->surveys) {
                return back()->with('toast', ['danger', '現在のユーザーでは一つ以上のアンケートを作成することはできません']);
            }
            $survey = $user->surveys()->create([
                'user_id' => $user->id,
                'title' => $request->title,
                'note' => $request->note,
                'voice_name' => config('app.voices')[0]['name']
            ]);
            return redirect("/surveys/{$survey->id}")->with('toast', ['success', 'アンケートを新規作成しました']);
        });
    });

    Route::prefix('/send_emails/{send_email}')->group(function () {
        Route::put('/', function (Request $request, SendEmail $send_email) {
            if ($request->user()->cannot('update', $send_email)) abort(403);
            $send_email->update([
                'email' => $request->email,
                'enabled' => $request->input('enabled', false)
            ]);
            return back()->with('toast', ['success', '送信先メールアドレスを変更しました']);
        });
        
        Route::delete('/', function (Request $request, SendEmail $send_email) {
            if ($request->user()->cannot('forceDelete', $send_email)) abort(403);
            $send_email->delete();
            return back()->with('toast', ['info', '送信先メールアドレスを削除しました']);
        });
    });

    Route::prefix('/surveys/{survey}')->group(function () {
        Route::get('/', function (Request $request, Survey $survey) {
            return view('survey', ['survey' => $survey]);
        });
    });
});
