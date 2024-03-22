<x-layout>
  <x-breadcrumb>
    <li class="breadcrumb-item"><a href="/">ホーム</a></li>
    <li class="breadcrumb-item"><a href="/surveys/{{ $survey->id }}">{{ $survey->title }}</a></li>
    <li class="breadcrumb-item active">会話と音声</li>
  </x-breadcrumb>
  <x-h2>{{ $survey->title }}: 会話と音声</x-h2>
<div class="d-flex gap-3">
  <div class="w-100">
    <section id="greeting-ending">
      <x-h3>グリーティング・エンディング</x-h3>
      <div class="form-text mb-2">
        グリーティングで通話の最初に流れるテキストを編集できます。<br>
        エンディングは回答の結果によって変更することができます。
      </div>
      <div class="card">
        <div class="card-body">
          <h5 class="card-title mb-3"><span class="badge bg-secondary-subtle text-black me-3">グリーティング</span></h5>
          <p class="card-text mb-0">{{ $survey->greeting }}</p>
          <div class="position-absolute top-0 end-0 p-3">
            <button type="button" class="btn btn-outline-dark icon-link me-2" data-bs-toggle="modal" data-bs-target="#greetingModal">
              <i class="fa-solid fa-volume-high"></i>
              設定と音声
            </button>
          </div>
          @if (!$survey->greeting_voice_file)
            <div class="alert alert-danger mt-3 mb-0" role="alert">
              グリーティングの読み上げ文章を更新して音声ファイルを生成してください
            </div>
          @endif
        </div>
      </div>
      <hr class="my-3">
      @if ($survey->endings->isNotEmpty())
        <div class="vstack gap-3 mb-3">
          @foreach ($survey->endings as $ending)
            <div class="card">
              <div class="card-body">
                <h5 class="card-title mb-3"><span class="badge bg-dark-subtle text-black me-2">
                  エンディング</span>{{ $ending->title }}
                </h5>
                <p class="card-text mb-0">{{ $ending->text }}</p>
                <div class="position-absolute top-0 end-0 p-3">
                  @if ($survey->success_ending_id === $ending->id)
                    <span class="badge bg-success me-2">成功</span>
                  @endif
                  <button type="button" class="btn btn-outline-dark icon-link me-2" data-bs-toggle="modal" data-bs-target="#endingModal{{ $ending->id }}">
                    <i class="fa-solid fa-volume-high"></i>
                    設定と音声
                  </button>
                </div>
                @if (!$ending->voice_file)
                  <div class="alert alert-danger mt-3 mb-0" role="alert">
                    エンディングの読み上げ文章を更新して音声ファイルを生成してください
                  </div>
                @endif
              </div>
            </div>
          @endforeach
        </div>
      @else
        <x-noContent>エンディングがありません</x-noContent>
      @endif
      <x-modalOpenButton target="endingsCreateModal" />
    </section>
    <hr class="my-5">
    <section id="faqs">
      <x-h3>質問一覧</x-h3>
      <div class="form-text mb-2">
        一番上に配置された質問が最初の質問（グリーティングの後に再生される質問）となります
      </div>
      @if ($survey->faqs->isNotEmpty())
        <div class="vstack gap-3 mb-3">
          @foreach ($survey->faqs as $faq)
            <div class="card" id="faq{{ $faq->id }}">
              <div class="card-body">
                <h5 class="card-title mb-3">
                  <span class="badge bg-primary-subtle text-black me-2">質問</span>{{ $faq->title }}
                </h5>
                <p class="card-text">{{ $faq->text }}</p>
                @if ($faq->options)
                  <table class="table table-sm mb-0">
                    <thead>
                      <tr>
                        <th scope="col">ダイヤル番号</th>
                        <th scope="col">TITLE</th>
                        <th scope="col">NEXT</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach ($faq->options as $option)
                      <tr>
                        <th scope="row"><span class="">{{ $option->dial }}</span></th>
                        <td>{{ $option->title }}</td>
                        <td>
                          @if ($option->next_faq)
                            @if ($option->next_faq["id"] !== $faq->id)
                              <a
                                href="/faqs/{{ $option->next_faq["id"] }}"
                                class="badge bg-primary-subtle text-black fw-normal"
                                style="text-decoration: none;"
                              >
                                {{ $option->next_faq["title"] }}
                              </a>
                            @else
                              <span class="badge bg-info-subtle text-black fw-normal">聞き直し</span>
                            @endif
                            @elseif ($option->next_ending)
                              <span class="badge bg-dark-subtle text-black fw-normal">{{ $option->next_ending->title }}</span>
                            @endif
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                @endif
                <div class="position-absolute top-0 end-0 py-2 px-3">
                  @if (!$faq->order_num)
                    <span class="badge bg-info me-2">最初の質問</span>
                  @endif
                  <form action="/faqs/{{ $faq->id }}/order" id="upFaq{{ $faq->id }}" method="post" hidden>
                    @csrf
                    <input type="hidden" name="to" value="up">
                  </form>
                  <form action="/faqs/{{ $faq->id }}/order" id="downFaq{{ $faq->id }}" method="post" hidden>
                    @csrf
                    <input type="hidden" name="to" value="down">
                  </form>
                  <div class="btn-group me-2" role="group" aria-label="Basic outlined example">
                    <button
                    type="submit"
                    class="btn btn-outline-primary" {{ $faq->order_num === 0 ? "disabled" : ""; }}
                    form="upFaq{{ $faq->id }}"
                    >
                      <i class="fa-solid fa-angle-up"></i>
                    </button>
                    <button
                    type="submit"
                    class="btn btn-outline-primary" {{ $faq->order_num === $survey->faqs->max('order_num') ? 'disabled' : ''; }}
                    form="downFaq{{ $faq->id }}"
                    >
                      <i class="fa-solid fa-angle-down"></i>
                    </button>
                  </div>
                  <a href="/faqs/{{ $faq->id }}" class="btn btn-outline-dark icon-link me-2">
                    <i class="fa-solid fa-volume-high"></i>
                    設定と音声
                  </a>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <x-noContent>質問がありません</x-noContent>
      @endif
      <x-modalOpenButton target="faqsCreateModal" />
    </section>
  </div>
  <div class="flex-shrink-0 sticky-aside" style="width: 300px;">
    <div class="sticky-top">
      <section id="setting">
        <x-h4>設定</x-h4>
        <div class="mb-2">
          <form method="post">
            @csrf
            @method('PUT')
            <div class="mb-3">
              <label class="form-label">アンケートのタイトル</label>
              <input type="text" name="title" class="form-control" value="{{ $survey->title }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">アンケートの説明（任意）</label>
              <textarea class="form-control" name="note" rows="3">{{ $survey->note }}</textarea>
            </div>
            <div class="mb-3">
              <label class="form-label">成功エンディング</label>
              <select class="form-select" name="success_ending_id">
                <option value="">--未設定--</option>
                @foreach ($survey->endings as $ending)
                  <option
                    value="{{ $ending->id }}"
                    {{ $ending->id === $survey->success_ending_id ? 'selected' : ''; }}
                  >
                  {{ $ending->title }}
                </option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">
                生成する音声のタイプ
                <x-infobtn />
              </label>
              <select class="form-select" name="voice_name">
                @foreach (config('app.voices') as $voice)
                  <option
                    value="{{ $voice['name'] }}"
                    {{ $voice['name'] === $survey->voice_name ? 'selected' : '' }}
                  >
                  {{ $voice['name'] }} ({{ $voice['gender'] }})
                </option>
                @endforeach
              </select>
            </div>
            <div class="text-end mb-2">
              <button type="submit" class="btn btn-dark">更新</button>
            </div>
            <div class="form-text">
              音声タイプの変更は、既に生成された音声ファイルには影響せず、既存の文章に反映させるには全て更新する必要があります
            </div>
          </form>
        </div>
        <div class="text-end">
          <button type="button" class="btn btn-outline-dark btn-sm" data-bs-toggle="modal" data-bs-target="#advancedSettingsModal">
            詳細設定
          </button>
        </div>
      </section>
    </div>
  </div>
</div>

<x-modal id="faqsCreateModal" title="質問を新規作成">
  <form action="/surveys/{{ $survey->id }}/faqs" method="post">
    @csrf
    <div class="mb-3">
      <label class="form-label">タイトル</label>
      <input type="text" name="title" class="form-control" placeholder="〇〇に関する質問" required>
    </div>
    <div class="mb-3">
      <label class="form-label">読み上げテキスト</label>
      <textarea name="text" class="form-control" rows="5" required></textarea>
    </div>
    <div class="text-end">
      <input type="hidden" name="survey_id" value="{{ $survey->id}}">
      <button type="submit" class="btn btn-primary">作成</button>
    </div>
    <div class="form-text">
      質問を作成すると自動的に「0: 聞き直し」の選択肢が設定されます
    </div>
  </form>
</x-modal>

<x-modal id="greetingModal" title="グリーティングを編集">
  <div class="text-center mb-3">
    <audio controls src="{{ $survey->greeting_voice_file_url() }}"></audio>
  </div>
  <form action="/surveys/{{ $survey->id }}/greeting" method="post">
    @csrf
    <div class="mb-3">
      <label class="form-label">テキスト</label>
      <textarea name="greeting" class="form-control" rows="5">{{ $survey->greeting }}</textarea>
    </div>
    <div class="text-end">
      <input type="hidden" name="survey_id" value="{{ $survey->id }}">
      <button type="submit" class="btn btn-primary">更新</button>
    </div>
    <div class="form-text">更新すると入力されたテキストから音声ファイルが自動的に生成されます</div>
  </form>
</x-modal>

<x-modal id="advancedSettingsModal" title="詳細設定">
  <div class="mb-3">
    <form
    action="/surveys/{{ $survey->id }}/all_voice_file_re_gen"
    method="post"
    onsubmit="return window.confirm('本当に実行しますか？この操作には時間がかかることがあります')"
    >
      @csrf
      <button class="btn btn-outline-danger">全ての音声ファイルを更新する</button>
      <div class="form-text">
        このアンケートのグリーティングや全てのエンディング、質問の音声ファイルが現在の設定で再生成されます。
      </div>
    </form>
  </div>
  <div class="mb-3">
    <form
    action="/surveys/{{ $survey->id }}"
    method="post"
    onsubmit="return window.confirm('本当に削除しますか？')"
    >
      @csrf
      @method('DELETE')
      <button class="btn btn-outline-danger">アンケートを削除する</button>
      <div class="form-text">
        このアンケートに関連する全てのデータ（質問、予約、結果など）が削除されます。
      </div>
    </form>
  </div>
</x-modal>

<x-modal id="endingsCreateModal" title="エンディングを作成">
  <form action="/surveys/{{ $survey->id }}/endings" method="post">
    @csrf
    <div class="mb-3">
      <label class="form-label">エンディングのタイトル</label>
      <input type="text" name="title" class="form-control" placeholder="〇〇のエンディング" required>
    </div>
    <div class="mb-3">
      <label class="form-label">エンディングのテキスト</label>
      <textarea name="text" class="form-control" rows="5" required></textarea>
    </div>
    <div class="text-end">
      <input type="hidden" name="survey_id" value="{{ $survey->id }}">
      <button type="submit" class="btn btn-primary">作成</button>
    </div>
  </form>
</x-modal>

@foreach ($survey->endings as $ending)
  <x-modal id="endingModal{{ $ending->id}}" title="エンディングを編集">
    <div class="text-center mb-3">
      <audio controls src="{{ $ending->voice_file_url() }}"></audio>
    </div>
    <form action="/endings/{{ $ending->id}}" method="post">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">エンディングのタイトル</label>
        <input type="text" name="title" class="form-control" value="{{ $ending->title }}">
      </div>
      <div class="mb-3">
        <label class="form-label">テキスト</label>
        <textarea name="text" class="form-control" rows="5">{{ $ending->text }}</textarea>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-primary">更新</button>
      </div>
      <div class="form-text">更新すると入力されたテキストから音声ファイルが自動的に生成されます</div>
    </form>
    <form action="/endings/{{ $ending->id }}" method="post"  onsubmit="return window.confirm('本当に削除しますか？')">
      @csrf
      @method('DELETE')
      <div class="text-end">
        <button type="submit" class="btn btn-link">このエンディングを削除</button>
      </div>
    </form>
  </x-modal>
@endforeach

  @if (Auth::user()->id !== $survey->user->id)
    <x-watchonadmin>管理者として閲覧専用でこのページを閲覧しています</x-watchonadmin>
  @endif
</x-layout>
