<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ config('app.name') }}</title>
	<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
	<link rel="stylesheet" href="{{ asset('css/style.css') }}">
	<link rel="stylesheet" href="{{ asset('css/markdown.css') }}">
	<script src="{{ asset('js/index.js') }}" defer></script>
	<script src="https://kit.fontawesome.com/285c1d0655.js" crossorigin="anonymous"></script>
<body style="background-color: <?= env('BACKGROUND_COLOR') ?>;">
	@php
	$survey_id = Auth::user()->survey->id;
	$menu_list = [
		["/home", 'ホーム', 'house'],
		["/surveys/{$survey_id}", '会話と音声', 'comments'],
		["/surveys/{$survey_id}/calendar", 'カレンダー', 'calendar'],
		["/surveys/{$survey_id}/stats", '統計', 'chart-simple'],
		["/surveys/{$survey_id}/asset", 'アセット', 'bookmark'],
		["/surveys/{$survey_id}/calls", 'コール一覧', 'phone'],
		["/support", 'ドキュメント', 'circle-question'],
	];
	$admin_menu_list = [
		["/admin/users", 'ユーザー管理', 'users'],
		["/admin/reserves", '全ての予約', 'table-list'],
		["/admin/gen_reserve_log", '予約情報ファイル生成ログ', 'file-arrow-up'],
		["/admin/receive_result_log", '結果ファイル受信ログ', 'file-arrow-down'],
	];
	$current_url_path = parse_url(URL::current(), PHP_URL_PATH);
	@endphp
<header class="sticky-top" id="header-navber">
	<nav class="navbar navbar-expand-md bg-body-tertiary">
		<div class="container-fluid">
			<a class="navbar-brand" href="/home">
				{{ config('app.name') }}
			</a>
			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarNav">
				<ul class="navbar-nav">
					@foreach ($menu_list as $item)
						<li class="nav-item">
							<a class="nav-link" href="{{ $item[0] }}">
								<span class="text-center d-inline-block me-1" style="width: 20px;">
									<i class="fa-solid fa-{{ $item[2] }}"></i>
								</span>{{ $item[1] }}
							</a>
						</li>
					@endforeach
          @if(Auth::user()->isAdmin())
						<li class="nav-item dropdown">
							<button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
								管理者メニュー
							</button>
							<ul class="dropdown-menu">
								@foreach ($admin_menu_list as $item)
									<li class="dropdown-item">
										<a href="{{ $item[0] }}" class="nav-link">
											<span class="text-center d-inline-block me-2" style="width: 20px;">
												<i class="fa-solid fa-{{ $item[2] }}"></i>
											</span>{{ $item[1] }}
										</a>
									</li>
								@endforeach
							</ul>
						</li>
					@endif
					<li class="nav-item dropdown">
						<button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
							{{ Auth::user()->email }}
						</button>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="/account">アカウント設定</a></li>
							<li><hr class="dropdown-divider"></li>
							<li>
								<form action="/logout" method="post">
									@csrf
									<button class="dropdown-item" href="/logout">ログアウト</button>
								</form>
							</li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</nav>
</header>
<div class="flex-container">
	<header class="border-end border-2 text-bg-white" id="header-sideber">
		<div class="sticky-top container vh-100 p-3 d-flex flex-column">
			<h1 class="fs-3 fw-bold mb-0">
				<a href="/home" class="text-black" style="text-decoration: none;">{{ config('app.name') }}</a>
			</h1>
			<hr>
			<nav id="navbar-example2" class="mb-auto">
				<ul class="nav nav-pills vstack gap-1">
					@foreach ($menu_list as $item)
						<li class="nav-item">
							<a
								class="nav-link {{ $current_url_path === $item[0] ? 'active' : 'link-body-emphasis' }}"
								href="{{ $item[0] }}"
							>
								<span class="text-center d-inline-block me-2" style="width: 24px;">
									<i class="fa-solid fa-{{ $item[2] }} fa-lg"></i>
								</span>{{ $item[1] }}
							</a>
						</li>
					@endforeach
					@if(Auth::user()->isAdmin())
						<li class="nav-item my-2 p-1 border border-2 rounded-2">
							<h4 class="fs-6">管理者メニュー</h4>
							<ul class="nav nav-pills vstack gap-1">
								@foreach ($admin_menu_list as $item)
									<li class="nav-item">
										<a href="{{ $item[0] }}" class="nav-link <?= request()->server->get('REDIRECT_URL') === "/admin/users" ? "active" : "link-body-emphasis" ?>">
											<span class="text-center d-inline-block me-2" style="width: 24px;">
												<i class="fa-solid fa-{{ $item[2] }} fa-lg"></i>
											</span>{{ $item[1] }}
										</a>
									</li>
								@endforeach
							</ul>
						</li>
					@endif
				</ul>
				@if(Auth::user()['role'] === 'SUSPENDED')
					<div class="alert alert-danger mt-2 mb-0" role="alert">
						<span class="text-danger">
							<i class="fa-solid fa-circle-exclamation"></i>
						</span>
						アカウントが利用停止されています<br>
						<small>ご利用を再開するには管理者に<a href="/support">お問い合わせ</a>ください</small>
					</div>
				@endif
			</nav>
			<hr>
			<div class="dropdown">
				<button class="btn btn-outline-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
					{{ Auth::user()->email }}
				</button>
				<ul class="dropdown-menu dropdown-menu-dark">
					<li><a class="dropdown-item" href="/users/{{ Auth::user()['id'] }}">アカウント設定</a></li>
					<li><hr class="dropdown-divider"></li>
					<li>
						<form action="/logout" method="post">
							@csrf
							<button class="dropdown-item" href="/logout">ログアウト</button>
						</form>
					</li>
				</ul>
			</div>
		</div>
	</header>
<main>
<div class="main-container position-relative">

<x-modal id="surveysCreateModal" title="アンケートを新規作成">
	<form action="/users/{{ Auth::user()->id }}/surveys" method="post">
		@csrf
		<div class="mb-3">
			<label class="form-label">アンケートのタイトル</label>
			<input type="text" name="title" class="form-control" placeholder="〇〇のアンケート"  required>
		</div>
		<div class="mb-3">
			<label class="form-label">アンケートの説明（任意）</label>
			<textarea class="form-control" name="note" rows="3"></textarea>
		</div>
		<div class="mb-3">
			<label class="form-label">
				生成する音声のタイプ
				<x-infobtn />
			</label>
			<select class="form-select" name="voice_name">
				@foreach (config('app.voices') as $voice)
					<option value="{{ $voice['name'] }}">
						{{ $voice['name'] }} ({{ $voice['gender'] }})
					</option>
				@endforeach
			</select>
		</div>
		<div class="text-end">
			<button type="submit" class="btn btn-primary">作成</button>
		</div>
	</form>
</x-modal>
