<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->comment('例：山田');
            $table->string('last_name')->comment('例：太郎');
            $table->tinyInteger('gender')->comment('1: 男性, 2: 女性, 3: その他');
            $table->string('email')->comment('例: test@example.com');
            $table->string('tell_1', 10)->comment('080');
            $table->string('tell_2', 10)->comment('1234');
            $table->string('tell_3', 10)->comment('5678');
            $table->string('address')->comment('例: 東京都渋谷区千駄ヶ谷1-2-3');
            $table->string('building_name')->nullable()->comment('例: 千駄ヶ谷マンション305');
            $table->foreignId('category_id')->constrained()->comment('選択してください');
            $table->text('body')->comment('お問い合わせ内容を入力してください');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
