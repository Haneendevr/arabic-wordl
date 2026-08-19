<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('game_stats', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->index(); // ربط بالـ Session لتحديد اللاعب بآمان دون حسابات
            $table->string('target_word', 5);
            $table->boolean('won')->default(false);
            $table->unsignedTinyInteger('attempts'); // تقييد القيم لمنع إدخال أرقام شاذة
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('game_stats');
    }
};