<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Exam Paper</title>

  <style>

   @font-face {
            font-family: 'NotoSansTamil';
            src: url("{{ storage_path('fonts/NotoSansTamil-Regular.ttf') }}") format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        @font-face {
            font-family: 'NotoSansTamil';
            src: url("{{ storage_path('fonts/NotoSansTamil-Bold.ttf') }}") format('truetype');
            font-weight: bold;
            font-style: normal;
        }

        body {
            font-family: 'NotoSansTamil', sans-serif;
            font-size: 12px;
        }
    @page { margin: 22mm 16mm; }

    body{
      font-family: "DejaVu Sans", sans-serif;
      font-size: 12px;
      color: #111;
    }

    /* ✅ watermark on every page */
    .wm{
      position: fixed;
      top: 45%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 420px;
      opacity: 0.06;
      z-index: -1000;
    }

    .head{
      margin-bottom: 10px;
    }
    .brand{
      font-size: 13px;
      letter-spacing: 2px;
      font-weight: 700;
      color: #0f766e;
    }
    .title{
      font-size: 22px;
      font-weight: 800;
      margin: 6px 0 2px;
    }
    .meta{
      color:#555;
      font-size: 12px;
      margin-top: 6px;
    }
    hr{
      border: none;
      border-top: 1px solid #ddd;
      margin: 12px 0;
    }

    .qbox{
      padding: 10px 0;
      border-bottom: 1px dashed #e5e7eb;
      page-break-inside: avoid;
    }
    .qno{
      font-weight: 800;
      margin-bottom: 6px;
    }
    .qtext{
      white-space: pre-line; /* ✅ keep A/B/C new lines */
      line-height: 1.45;
      margin-bottom: 8px;
    }
    .opt{
      margin: 4px 0;
    }
    .opt b{
      display:inline-block;
      width: 18px;
    }
  </style>
</head>

<body>
  @if(!empty($watermark))
    <img src="{{ $watermark }}" class="wm" alt="watermark">
  @endif

  <div class="head">
    <div class="brand">EXAMPORTAL</div>
    <div class="title">{{ $exam->title }}</div>
    <div class="meta">
      Attempt ID: {{ $attempt->id }} &nbsp;•&nbsp;
      Total Questions: {{ $orderedQuestions->count() }}
    </div>
    <hr>
  </div>

  @foreach($orderedQuestions as $i => $q)
    <div class="qbox">
      <div class="qno">Q{{ $i+1 }}.</div>

      <div class="qtext">{{ $q->question_text }}</div>

      @foreach($q->options as $opt)
        <div class="opt">
          <b>{{ $opt->option_key }}.</b>
          <span>{{ trim($opt->option_text ?? '') !== '' ? $opt->option_text : ' ' }}</span>
        </div>
      @endforeach
    </div>
  @endforeach
</body>
</html>
