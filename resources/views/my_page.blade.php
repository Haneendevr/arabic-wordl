<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>صفحتي الخاصة</title>
    <style>
        body { font-family: sans-serif; text-align: center; margin-top: 80px; background-color: #f3f4f6; }
        .card { background: white; padding: 30px; display: inline-block; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #2563eb; }
    </style>
</head>
<body>
    <div class="card">
        <h1>أهلاً بكِ يا {{ $userName }}! 🌟</h1>
        <p>هذه أول صفحة يتم عرضها باستخدام Controller و Blade View بنفسك!</p>
    </div>
</body>
</html>