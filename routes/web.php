<?php

use App\Http\Controllers\FaqController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
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
        Route::controller(UserController::class)->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::post('/send_emails', 'store_send_email');
            Route::put('/password', 'update_password');
            Route::post('/surveys', 'store_survey');
        });
    });

    Route::prefix('/send_emails/{send_email}')->group(function () {
        Route::controller(SendEmailController::class)->group(function () {
            Route::put('/', 'update');
            Route::delete('/', 'destroy');
        });
    });

    Route::prefix('/surveys/{survey}')->group(function () {
        Route::controller(SurveyController::class)->group(function () {
            Route::get('/', 'show');
        });
    });

    Route::prefix('/faqs/{faq}')->group(function () {
        Route::controller(FaqController::class)->group(function () {
            Route::get('/', 'show');
            Route::put('/', 'update');
            Route::delete('/', 'destroy');
        });
    });
});
