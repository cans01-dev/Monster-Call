<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    public function index () {
        $users = User::all();
        $roles = Role::all();
        return view('pages.admin.users', ['users' => $users, 'roles' => $roles]);
    }

    public function store (Request $request) {
        if ($request->password !== $request->password_confirm) {
            return back()->with('toast', ['danger', 'パスワードの再入力が正しくありません'])->withInput();
        }
        if (User::where('email', $request->email)->exists()) {
            return back()->with('toast', ['danger', 'このメールアドレスは既に使用されています'])->withInput();
        }

        $user = DB::transaction(function () use ($request) {
            $user = User::create([
                'email' => $request->email,
                'password' => password_hash($request->password, PASSWORD_DEFAULT),
                'status' => $request->status,
                'number_of_lines' => $request->number_of_lines,
                'name' => $request->name,
                'role_id' => $request->role_id
            ]);
    
            $user->send_emails()->create([
                'email' => $user->email,
                'enabled' => 1
            ]);
    
            $survey = $user->survey()->create([
                'title' => 'アンケート１',
                'note' => 'デフォルトのアンケート',
                'greeting' => 'こんにちは、これはサンプルのアンケートです',
                'voice_name' => 'ja-JP-Standard-A'
            ]);
    
            $faq = $survey->faqs()->create([
                'title' => 'デフォルトの質問',
                'text' => 'これはデフォルトの質問です、もう一度お聞きになりたい方は０を押してください。',
                'order_num' => 0
            ]);
    
            $faq->options()->create([
                'title' => '聞き直し',
                'dial' => 0,
                'next_faq_id' => $faq->id
            ]);

            return $user;
        });

        Storage::makeDirectory("users/{$user->id}");
        
        // $survey = Fetch::find('surveys', $survey_id);
        // avfrg($survey);
      
        
        // DB::insert('favorites', [
        //     'survey_id' => $survey_id,
        //     'title' => '予約パターン１',
        //     'color' => '#DCF2F1',
        //     'start' => '17:00:00',
        //     'end' => '21:00:00'
        // ]);
        
        // DB::insert('favorites_areas', [
        //     'favorite_id' => $favorite_id,
        //     'area_id' => 1
        // ]);

        return back()->with('toast', ['success', 'ユーザーを新規作成しました']);
    }

    public function update (Request $request, User $user) {
        $user->update([
            'role_id' => $request->role_id,
            'number_of_lines' => $request->number_of_lines
        ]);
        return back()->with('toast', ['success', 'ユーザー情報を変更しました']);
    }

    public function update_password (Request $request, User $user) {
        if ($request->new_password !== $request->new_password_confirm) {
            back()->with('toast', ['success', 'パスワードの再入力が正しくありません']);
        }
        $user->update([
            'password' => $request->password
        ]);
        back()->with('toast', ['success', 'パスワードを変更しました']);
    }

    public function clean_dir(User $user) {
        $files = Storage::files("users/{$user->id}");
        $files_count = count($files);

        foreach ($files as $file) Storage::delete($file);

        return back()->with("toast", ["success", "{$user->email}のディレクトリ内{$files_count}件のファイルを削除しました"]);
    }
}
