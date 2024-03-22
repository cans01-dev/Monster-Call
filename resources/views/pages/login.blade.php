<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ config('app.name') }}</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('css/sign-in.css') }}">
  <script src="{{ asset('js/index.js') }}"></script>
	<script src="https://kit.fontawesome.com/285c1d0655.js" crossorigin="anonymous"></script>
<body class="d-flex align-items-center py-4 bg-body-tertiary">
    
<main class="form-signin w-100 m-auto">
  <form action="/login" method="post">
    @csrf
    <h1 class="h3 mb-3 fw-normal">ログインしてください</h1>
    <div class="form-floating">
      <input type="email" class="form-control" name="email" placeholder="" value="{{ old('email') }}" required>
      <label for="floatingInput">メールアドレス</label>
    </div>
    <div class="form-floating mb-3">
      <input type="password" class="form-control" name="password" placeholder="" required>
      <label for="floatingPassword">パスワード</label>
    </div>
    <button class="btn btn-primary w-100 py-2" type="submit">ログイン</button>
    <p class="mt-5 mb-3 text-body-secondary">&copy; AutoCallシステム</p>
  </form>
</main>

<x-toast />
<script src="https://kit.fontawesome.com/285c1d0655.js" crossorigin="anonymous"></script>
</body>
</html>