<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\EndingController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\SendEmailController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
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
    return view('pages.login');
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
        return view('pages.home');
    })->name('home');
    
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    
        return redirect('/login')->with('toast', ['info', 'ログアウトしました']);
    });

    Route::prefix('/users/{user}')->controller(UserController::class)->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::post('/send_emails', 'store_send_email');
        Route::put('/password', 'update_password');
        Route::post('/surveys', 'store_survey');
    });

    Route::prefix('/send_emails/{send_email}')->controller(SendEmailController::class)->group(function () {
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    });

    Route::prefix('/surveys/{survey}')->controller(SurveyController::class)->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::post('/endings', 'store_ending');
        Route::post('/faqs', 'store_faq');
        Route::post('/greeting', 'update_greeting');
    });

    Route::prefix('/endings/{ending}')->controller(EndingController::class)->group(function () {
        Route::put('/', 'update');
    });

    Route::prefix('/faqs/{faq}')->controller(FaqController::class)->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
        Route::delete('/', 'destroy');
    });

    Route::middleware(['admin'])->prefix('/admin')->group(function () {
        Route::prefix('/users')->controller(AdminUserController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/', 'store');
            Route::put('/{user}', 'update');
            Route::delete('/{user}', 'destroy');
            Route::post('/{user}/password', 'update_password');
            Route::post('/{user}/clean_dir', 'clean_dir');
        });
    });
});
