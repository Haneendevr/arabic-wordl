<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
  <!-- Google tag (gtag.js) -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=G-L0NJH57MT9"></script>
  <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', 'G-L0NJH57MT9');
  </script>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>خَمّن - لعبة كلمات عربية</title>
  <style>
    .stats-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      background-color: #1a1a1b;
      padding: 12px 20px;
      border-radius: 8px;
      border: 1px solid #3a3a3c;
      margin-bottom: 20px;
    }

    .stat-box {
      text-align: center;
    }

    .stat-value {
      font-size: 1.4rem;
      font-weight: bold;
      color: #538d4e;
    }

    .stat-label {
      font-size: 0.75rem;
      color: #818384;
      margin-top: 2px;
    }

    * {
      box-sizing: border-box;
      font-family: system-ui, -apple-system, sans-serif;
    }

    body {
      background-color: #121213;
      color: white;
      display: flex;
      flex-direction: column;
      align-items: center;
      min-height: 100vh;
      margin: 0;
      padding: 20px;
    }

    h1 {
      margin-bottom: 5px;
      font-size: 2.2rem;
    }

    .sub {
      color: #818384;
      font-size: 0.9rem;
      margin-bottom: 25px;
    }

    .grid {
      display: grid;
      grid-template-rows: repeat(6, 1fr);
      gap: 6px;
      margin-bottom: 25px;
    }

    .row {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 6px;
    }

    .tile {
      width: 52px;
      height: 52px;
      border: 2px solid #3a3a3c;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
      font-weight: bold;
      border-radius: 4px;
    }

    .tile.correct {
      background-color: #538d4e;
      border-color: #538d4e;
    }

    .tile.present {
      background-color: #b59f3b;
      border-color: #b59f3b;
    }

    .tile.absent {
      background-color: #3a3a3c;
      border-color: #3a3a3c;
    }

    form {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 15px;
    }

    .input-box {
      display: flex;
      gap: 10px;
    }

    input[type="text"] {
      background-color: #272729;
      border: 1px solid #4d4d4f;
      color: white;
      padding: 12px 15px;
      font-size: 1.2rem;
      text-align: center;
      border-radius: 6px;
      outline: none;
      width: 180px;
      letter-spacing: 2px;
    }

    button {
      background-color: #538d4e;
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1rem;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
    }

    .alert {
      padding: 15px 25px;
      border-radius: 8px;
      text-align: center;
      margin-bottom: 20px;
      background-color: #272729;
      border: 1px solid #3a3a3c;
    }

    .alert-error {
      background-color: #7f1d1d;
      border-color: #991b1b;
      color: #fecaca;
      margin-bottom: 15px;
      padding: 10px 20px;
      border-radius: 6px;
    }
.toast-error {
    background-color: #272729;
    border: 1px solid #e11d48;
    color: #fda4af;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 0.85rem;
    margin-bottom: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    animation: fadeIn 0.3s ease-in-out;
}
    .btn-new {
      background-color: #2563eb;
      text-decoration: none;
      display: inline-block;
      padding: 10px 20px;
      border-radius: 6px;
      color: white;
      font-weight: bold;
      margin-top: 10px;
      border: none;
      cursor: pointer;
    }
  </style>
</head>

<body>

  <h1>خَمّن 🔠</h1>
  <div class="stats-container">
    <div class="stat-box">
        <div class="stat-value">{{ $stats['total'] }}</div>
        <div class="stat-label">الجولات</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $stats['rate'] }}%</div>
        <div class="stat-label">نسبة الفوز</div>
    </div>
    <div class="stat-box">
        <div class="stat-value">{{ $stats['won'] }}</div>
        <div class="stat-label">أنتصار</div>
    </div>
</div>
  <div class="sub">خمن الكلمة العربية الخماسية في 6 محاولات</div>

  {{-- عرض أخطاء التحقق مثل إدخال أقل أو أكثر من 5 أحرف --}}
  @if ($errors->any())
  <div class="alert-error">
    @foreach ($errors->all() as $error)
    <div>{{ $error }}</div>
    @endforeach
  </div>
  @endif

  @if($gameOver)
  <div class="alert">
    @if($won)
    <h2>🎉 مبروك! إجابة صحيحة!</h2>
    @else
    <h2>❌ انتهت المحاولات!</h2>
    <p>الكلمة الصحيحة هي: <strong>{{ $targetWord }}</strong></p>
    @endif

    {{-- تحويل زر اللعبة الجديدة لنموذج آمن مع محمي بـ CSRF --}}
    <form action="/new-game" method="GET">
      <button type="submit" class="btn-new">لعبة جديدة 🔄</button>
    </form>
  </div>
  @endif

  <div class="grid">
    @for($r = 0; $r < 6; $r++)
      <div class="row">
      @php
      $guessData = $guesses[$r] ?? null;
      $wordChars = $guessData ? mb_str_split($guessData['word']) : [];
      @endphp

      @for($c = 0; $c < 5; $c++)
        @php
        $char=$wordChars[$c] ?? '' ;
        $status=$guessData['result'][$c] ?? '' ;
        @endphp
        <div class="tile {{ $status }}">{{ $char }}</div>
  @endfor
  </div>
  @endfor
  </div>

  @if(!$gameOver)
  <form action="/guess" method="POST" novalidate>
    @csrf
      <input type="text" name="guess" maxlength="5" minlength="5" required placeholder="ادخل الكلمة" autofocus autocomplete="off" novalidate>
      <button type="submit">تخمين</button>
    </div>
  </form>
  @endif

</body>

</html>