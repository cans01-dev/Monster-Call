<x-layout>
  <div class="pt-3">
    <x-h2>ユーザー管理</x-h2>
  </div>
  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>ユーザー名</th>
        <th>メールアドレス</th>
        <th>ステータス</th>
        <th>回線数</th>
        <th>操作</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($users as $user)
        <tr>
          <th>{{ $user->id }}</th>
          <td>{{ $user->name }}</td>
          <td><a href="/users/{{ $user->id }}">{{ $user->email }}</a></td>
          <td>
            <span class="badge bg-{{ $user->role->bg }}">
              {{ $user->role->title }}
            </span>
          </td>
          <td>{{ $user->number_of_lines }}</td>
          <td>
            <form
              action="/admin/users/{{ $user->id }}/clean_dir"
              method="post" id="cleanUser{{ $user->id }}DirForm"
              onsubmit="return window.confirm('本当に削除しますか？\r\n削除を実行するとユーザーの予約情報ファイル、音声ファイルが削除されます。')"
            >
              @csrf
            </form>
            <div class="btn-group">
              <button
                type="button"
                class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#editModal{{ $user->id }}"
              >
                編集
              </button>
              <button
                type="button"
                class="btn btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#changePasswordModal{{ $user->id }}"
              >
                パスワードを変更
              </button>
              <button class="btn btn-outline-primary" form="cleanUser{{ $user->id }}DirForm">ディレクトリをクリーニング</button>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <x-modalOpenButton target="usersCreateModal" />

  @foreach ($users as $user)
    <x-modal id="changePasswordModal{{ $user->id }}" title="パスワードを変更">
      <form action="/admin/users/{{ $user->id }}/password" method="post">
        @csrf
        <div class="mb-3">
          <label class="form-label"><b>{{ $user->email }}</b>の新しいパスワード</label>
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

    <x-modal id="editModal{{ $user->id }}" title="ユーザー情報を編集">
      <form action="/admin/users/{{ $user->id }}" method="post">
        @csrf
        @method('PUT')
        <div class="mb-3">
          <label class="form-label"><b>{{ $user->email }}</b>のステータス</label>
          <select class="form-select" name="role_id">
            @foreach ($roles as $role)
              <option value="{{ $role->id }}" @selected($role->id == $user->role_id)>{{ $role->title }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label"><b>{{ $user->email }}</b>の回線数</label>
          <input
          type="number" name="number_of_lines"
          class="form-control"min="1" max="300" required
          value="{{ $user->number_of_lines }}"
          >
        </div>
        <div class="form-text">
          回線数は1~300の間で指定する必要があります
        </div>
        <div class="text-end">
          <input type="hidden" name="user_id" value="{{ $user->id }}">
          <button type="submit" class="btn btn-success">更新</button>
        </div>
      </form>
      <form action="/admin/users/{{ $user->id }}" method="post" onsubmit="return window.confirm('本当に削除しますか？\r\n削除を実行するとユーザーの保有する質問や予約などのデータが削除されます')">
        @csrf
        @method('DELETE')
        <div class="text-end">
          <button class="btn btn-link">このユーザーを削除</button>
        </div>
      </form>
    </x-modal>
  @endforeach

  <x-modal id="usersCreateModal" title="ユーザーを新規作成">
    <form action="/admin/users" method="post">
      @csrf
      <div class="mb-3">
        <label class="form-label">ステータス</label>
        <select class="form-select" name="role_id">
          @foreach ($roles as $role)
            <option value="{{ $role->id }}" @selected($role->id == old('role_id'))>{{ $role->title }}</option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">ユーザー名</label>
        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">回線数</label>
        <input
        type="number" name="number_of_lines"
        class="form-control"min="1" max="300" value="{{ old('number_of_lines') }}" required
        >
      </div>
      <div class="mb-3">
        <label class="form-label">メールアドレス</label>
        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">パスワード</label>
        <input type="password" name="password" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">パスワード（再入力）</label>
        <input type="password" name="password_confirm" class="form-control" required>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-success">作成</button>
      </div>
      <div class="form-text">
        パスワードは8文字以上の半角英数字を指定してください
      </div>
    </form>
  </x-modal>
</x-layout>