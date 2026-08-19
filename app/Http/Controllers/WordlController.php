<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Word;
use Illuminate\Support\Facades\DB;

class WordlController extends Controller
{
  public function index(Request $request)
  {
    if (!$request->session()->has('target_word')) {
      $this->startNewGame($request);
    }

    $sessionId = $request->session()->getId();

    // حساب إحصائيات الجلسة الحالية من قاعدة البيانات بآمان
    $totalGames = DB::table('game_stats')->where('session_id', $sessionId)->count();
    $wonGames = DB::table('game_stats')->where('session_id', $sessionId)->where('won', true)->count();
    $winRate = $totalGames > 0 ? round(($wonGames / $totalGames) * 100) : 0;

    return view('wordl', [
      'guesses'    => $request->session()->get('guesses', []),
      'gameOver'   => $request->session()->get('game_over', false),
      'won'        => $request->session()->get('won', false),
      'targetWord' => $request->session()->get('game_over', false) ? $request->session()->get('target_word') : null,
      'stats'      => [
        'total' => $totalGames,
        'won'   => $wonGames,
        'rate'  => $winRate,
      ]
    ]);
  }

  public function makeGuess(Request $request)
  {
    if ($request->session()->get('game_over', false)) {
      return redirect('/');
    }

    // 1. XSS Protection: تنظيف النص
    $cleanGuess = strip_tags(trim($request->input('guess')));
    $request->merge(['guess' => $cleanGuess]);

    // 2. Strict Input Validation
    $request->validate([
      'guess' => 'required|string|size:5',
    ], [
      'guess.required' => '⚠️ لطفاً اكتب الكلمة أولاً!',
      'guess.size'     => '⚠️ الكلمة يجب أن تتكون من 5 أحرف',
    ]);

    $guess = $cleanGuess;
    $targetWord = $request->session()->get('target_word');
    $guesses = $request->session()->get('guesses', []);

    $evaluation = $this->evaluateGuess($guess, $targetWord);
    $guesses[] = [
      'word' => $guess,
      'result' => $evaluation
    ];

    $request->session()->put('guesses', $guesses);

    $isWon = ($guess === $targetWord);
    $isGameOver = $isWon || (count($guesses) >= 6);

    if ($isGameOver) {
      $request->session()->put('game_over', true);
      $request->session()->put('won', $isWon);

      // 3. التسجيل الآمن في قاعدة البيانات (يتم لمرة واحدة فقط لكل جولة)
      if (!$request->session()->get('stats_recorded', false)) {
        DB::table('game_stats')->insert([
          'session_id'  => $request->session()->getId(),
          'target_word' => $targetWord,
          'won'         => $isWon,
          'attempts'    => count($guesses),
          'created_at'  => now(),
          'updated_at'  => now(),
        ]);
        $request->session()->put('stats_recorded', true);
      }
    }

    return redirect('/');
  }

  public function startNewGame(Request $request)
  {
    $randomWord = Word::inRandomOrder()->first();
    $target = $randomWord ? $randomWord->word : 'طاولة';

    $request->session()->put('target_word', $target);
    $request->session()->put('guesses', []);
    $request->session()->put('game_over', false);
    $request->session()->put('won', false);
    $request->session()->forget('stats_recorded'); // إعادة ضبط حالة التنسيق للجولة الجديدة

    return redirect('/');
  }

  private function evaluateGuess($guess, $target)
  {
    $guessChars = mb_str_split($guess);
    $targetChars = mb_str_split($target);

    // التأكد من أن الكلمتين يحتويان على 5 عناصر لتفادي خطأ الأوفست
    $guessChars = array_pad(array_slice($guessChars, 0, 5), 5, '');
    $targetChars = array_pad(array_slice($targetChars, 0, 5), 5, '');

    $result = array_fill(0, 5, 'absent');

    // 1. مطابقة الحروف الصحيحة بالمكان الصحيح (أخضر)
    for ($i = 0; $i < 5; $i++) {
      if ($guessChars[$i] !== '' && $guessChars[$i] === $targetChars[$i]) {
        $result[$i] = 'correct';
        $targetChars[$i] = null; // تعليم الحروف المستعملة
      }
    }

    // 2. مطابقة الحروف الصحيحة بمكان خاطئ (أصفر)
    for ($i = 0; $i < 5; $i++) {
      if ($result[$i] !== 'correct' && $guessChars[$i] !== '') {
        $key = array_search($guessChars[$i], $targetChars);
        if ($key !== false && $targetChars[$key] !== null) {
          $result[$i] = 'present';
          $targetChars[$key] = null;
        }
      }
    }

    return $result;
  }
}
