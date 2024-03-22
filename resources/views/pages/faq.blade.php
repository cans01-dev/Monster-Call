<x-layout>
  <x-breadcrumb>
    <li class="breadcrumb-item"><a href="/">ホーム</a></li>
    <li class="breadcrumb-item"><a href="/surveys/{{ $faq->survey->id }}">{{ $faq->survey->title }}</a></li>
    <li class="breadcrumb-item active">質問: {{ $faq->title }}</li>
  </x-breadcrumb>
  <x-h2>質問: {{ $faq->title }}</x-h2>
  <div class="d-flex gap-3">
    <div class="w-100">
      <section id="summary">
        <x-h3>設定</x-h3>
        <form method="post">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label">質問のタイトル</label>
            <input type="text" name="title" class="form-control" value="{{ $faq->title }}">
          </div>
          <div class="mb-3">
            <label class="form-label">質問の読み上げ文章</label>
            <textarea class="form-control" name="text" rows="8">{{ $faq->text }}</textarea>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-dark">更新</button>
          </div>
          <div class="form-text">更新すると入力されたテキストから音声ファイルが自動的に生成されます</div>
        </form>
        <form method="post" onsubmit="return window.confirm('本当に削除しますか？\r\n遷移先がこの質問になっている他の質問の選択肢は自動的に「聞き直し」に変更されます')">
          @csrf
          @method('DELETE')
          <div class="text-end">
            <input type="submit" class="btn btn-link" value="この質問を削除">
          </div>
        </form>
      </section>
      <hr class="ym-3">
      <section id="options">
        <x-h3>選択肢</x-h3>
        <table class="table">
          <thead>
            <tr>
              <th scope="col">ダイヤル番号</th>
              <th scope="col">TITLE</th>
              <th scope="col">NEXT</th>
              <th scope="col">操作</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($faq->options as $option)
            <tr>
              <td>{{ $option->dial }}</td>
              <td>{{ $option->title }}</td>
              <td>
                <form action="/options/{{ $option->id }}" method="post">
                  @csrf
                  @method('PUT')
                  <input type="hidden" name="title" value="{{ $option->title }}" required>
                  <select class="form-select" name="next" onchange="submit(this.form)" required>
                    @foreach ($faq->survey["faqs"] as $f)
                      <option value="f{{ $f["id"] }}" {{ $option->next_faq_id === $f["id"] ? "selected" : ""; }}>
                        {{ $f["id"] === $option->faq_id ? "【聞き直し】": "【質問】"; }}{{ $f["title"] }}
                      </option>
                    @endforeach
                    @foreach ($faq->survey->endings as $ending)
                      <option value="e{{ $ending->id }}" {{ $option->next_ending_id === $ending->id ? "selected" : ""; }}>
                        【エンディング】{{ $ending->title }}
                      </option>
                    @endforeach
                  </select>
                </form>
              </td>
              <td>
                <button
                  type="button"
                  class="btn btn-primary"
                  data-bs-toggle="modal"
                  data-bs-target="#optionsEditModal{{ $option->id }}"
                >
                  編集
                </button>
                <form action="/options/{{ $option->id }}/order" id="upOption{{ $option->id }}" method="post" hidden>
                  @csrf
                  <input type="hidden" name="to" value="up">
                </form>
                <form action="/options/{{ $option->id }}/order" id="downOption{{ $option->id }}" method="post" hidden>
                  @csrf
                  <input type="hidden" name="to" value="down">
                </form>
                <div class="btn-group" role="group" aria-label="Basic outlined example">
                  <button
                  type="submit"
                  class="btn btn-outline-primary" {{ $option->dial === 0 ? "disabled" : ""; }}
                  form="upOption{{ $option->id }}"
                  >
                    <i class="fa-solid fa-angle-up"></i>
                  </button>
                  <button
                  type="submit"
                  class="btn btn-outline-primary" {{ $option->dial === $faq->options->max("dial") ? "disabled" : ""; }}
                  form="downOption{{ $option->id }}"
                  >
                    <i class="fa-solid fa-angle-down"></i>
                  </button>
                </div>
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        <x-modalOpenButton target="optionsCreateModal" />
      </section>
    </div>
    <div class="flex-shrink-0 sticky-aside" style="width: 300px;">
      <div class="sticky-top">
        <section id="summary">
          <x-h4>音声</x-h4>
          @if ($faq->voice_file)
            <div class="text-center py-2">
              <audio controls src="{{ $faq->voice_file_url() }}"></audio>
            </div>
          @else
            <x-noContent>音声ファイルがありません</x-noContent>
          @endif
        </section>
      </div>
    </div>
  </div>
  
<x-modal id="faqsCreateModal" title="質問を新規作成">
  <form action="/faqs" method="post">
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
      <input type="hidden" name="survey_id" value="{{ $faq->survey->id}}">
      <button type="submit" class="btn btn-primary">作成</button>
    </div>
    <div class="form-text">
      質問を作成すると自動的に「0: 聞き直し」の選択肢が設定されます
    </div>
  </form>
</x-modal>

<x-modal id="optionsCreateModal" title="選択肢を新規作成">
  <form action="/options" method="post">
    @csrf
    <div class="mb-3">
      <label class="form-label">選択肢のタイトル</label>
      <input type="text" name="title" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">NEXT</label>
      <select class="form-select" name="next" required>
        @foreach ($faq->survey["faqs"] as $f)
          <option value="f{{ $f["id"] }}" {{ $option->next_faq_id === $f["id"] ? "selected" : ""; }}>
            {{ $f["id"] === $option->faq_id ? "【聞き直し】": "【質問】"; }}{{ $f["title"] }}
          </option>
        @endforeach
        @foreach ($faq->survey->endings as $ending)
          <option value="e{{ $ending->id }}" {{ $option->next_ending_id === $ending->id ? "selected" : ""; }}>
            【エンディング】{{ $ending->title }}
          </option>
        @endforeach
      </select>
      <div id="passwordHelpBlock" class="form-text">
        この選択肢が選択された場合の次の操作を指定してください
      </div>
    </div>
    <div class="mb-3">
      <label class="form-label">ダイヤル番号</label>
      <input type="number" class="form-control" value="{{ count($faq->options) ? $faq->options->max("dial") + 1 : 0 }}" disabled>
      <div id="passwordHelpBlock" class="form-text">
        ダイヤル番号は作成後に質問ページの選択肢一覧から変更できます
      </div>
    </div>
    <div class="text-end">
      <input type="hidden" name="faq_id" value="{{ $faq->id }}">
      <button type="submit" class="btn btn-primary">作成</button>
    </div>
  </form>
</x-modal>

@foreach ($faq->options as $option)
  <x-modal id="optionsEditModal{{ $option->id }}" title="選択肢: {{ $option->title }}">
    <form method="post" action="/options/{{ $option->id }}">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label class="form-label">選択肢のタイトル</label>
        <input type="text" name="title" class="form-control" value="{{ $option->title }}" required>
      </div>
      <div class="mb-3">
        <label class="form-label">次の操作</label>
        <select class="form-select" name="next" onchange="submit(this.form)" required>
          @foreach ($faq->survey["faqs"] as $f)
            <option value="f{{ $f["id"] }}" {{ $option->next_faq_id === $f["id"] ? "selected" : ""; }}>
              {{ $f["id"] === $option->faq_id ? "【聞き直し】": "【質問】"; }}{{ $f["title"] }}
            </option>
          @endforeach
          @foreach ($faq->survey->endings as $ending)
            <option value="e{{ $ending->id }}" {{ $option->next_ending_id === $ending->id ? "selected" : ""; }}>
              【エンディング】{{ $ending->title }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">ダイヤル番号</label>
        <input type="number" class="form-control" value="{{ $option->dial }}" disabled>
        <div id="passwordHelpBlock" class="form-text">
          ダイヤル番号は並び替えで変更できます
        </div>
      </div>
      <div class="text-end">
        <button type="submit" class="btn btn-dark">更新</button>
      </div>
    </form>
    <form method="post" action="/options/{{ $option->id }}" onsubmit="return window.confirm('本当に削除しますか？\r\n削除を実行すると空いたダイヤル番号に他の選択肢が割り当てられます')">
      @csrf
      @method('DELETE')
      <div class="text-end">
        <input type="submit" class="btn btn-link" value="この選択肢を削除">
      </div>
    </form>
  </x-modal>
@endforeach


  @if (Auth::user()->id !== $faq->survey->user->id)
    <x-watchonadmin>管理者として閲覧専用でこのページを閲覧しています</x-watchonadmin>
  @endif
</x-layout>
