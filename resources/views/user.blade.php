<x-layout>
  <nav aria-label="breadcrumb" class="breadcrumb-nav">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="/">ホーム</a></li>
      <li class="breadcrumb-item active">アカウント</li>
    </ol>
  </nav>
  <x-h2>アカウント</x-h2>
  <div class="d-flex gap-3">
    <div class="w-100">
      <section id="summary">
        <x-h3>設定</x-h3>
        <div style="max-width: 480px;">
          <form action="/users/{{ $user['id'] }}" method="post">
            @csrf()
            @method('PUT')
            <div class="mb-3">
              <label class="form-label">ステータス</label>
              <div>
                <span class="badge fs-6 bg-{{ $user['status'] }}">
                  {{ $user['role']; }}
                </span>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">ユーザー名</label>
              <input type="text" name="name" class="form-control" value="{{ $user['name'] }}">
            </div>
            <div class="mb-3">
              <label class="form-label">メールアドレス</label>
              <input type="email" name="email" class="form-control" value="{{ $user['email'] }}">
            </div>
            <div class="mb-3">
              <label class="form-label">回線数</label>
              <input type="numbers" name="number_of_lines" class="form-control" value="{{ $user['number_of_lines'] }}" disabled>
            </div>
            <div class="mb-3">
              <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                パスワードを変更する
              </button>
            </div>
            <div class="text-end">
              <button type="submit" class="btn btn-dark">更新</button>
            </div>
          </form>
        </div>
      </section>
    </div>
    <div class="flex-shrink-0 sticky-aside" style="width: 300px;">
      <section id="sendEmails">
        <x-h3>送信先メールアドレス</x-h3>
        <div style="max-width: 480px;">
          @if ($user["sendEmails"])
            @foreach ($user["sendEmails"] as $sendEmail)
              <div class="card mb-2">
                <div class="card-body">
                  <span class="fw-bold me-2">{{ $sendEmail['email'] }}</span>
                  @if ($sendEmail["enabled"])
                    <span class="badge text-bg-dark">有効</span>
                  @else
                    <span class="badge text-bg-secondary">無効</span>
                  @endif
                  <div class="position-absolute top-0 end-0 p-3">
                    <a href="/send-emails/{{ $sendEmail['id'] }}" class="card-link">編集</a>
                  </div>
                </div>
              </div>
            @endforeach
          @else
            <x-nocontent>送信先メールがありません</x-nocontent>
          @endif
          <x-modalOpenButton :id="sendEmailsCreateModal" />
        </div>
      </section>
    </div>
  </div>

  <x-modal :id="changePasswordModal" :title="パスワードを変更">
    <form action="/account/password" method="post">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">現在のパスワード</label>
        <input type="password" name="old_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">新しいパスワード</label>
        <input type="password" name="new_password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">新しいパスワード（再入力）</label>
        <input type="password" name="new_password_confirm" class="form-control" required>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-primary">更新</button>
      </div>
      <div class="form-text">
        パスワードは8文字以上の半角英数字を指定してください
      </div>
    </form>
  </x-modal>

  <x-modal :id="sendEmailsCreateModal" :title="送信先メールアドレス新規登録">
    <form action="/users/{{ $user['id'] }}/send-emails" method="post">
      @csrf
      <div class="mb-3">
        <label class="form-label">メールアドレス</label>
        <input type="email" name="email" class="form-control" required>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-primary">登録</button>
      </div>
    </form>
  </x-modal>

  @if (Auth::user()["id"] !== $user["id"])
    <x-watchonadmin>管理者として閲覧専用でこのページを閲覧しています</x-watchonadmin>
  @endif
</x-layout>
