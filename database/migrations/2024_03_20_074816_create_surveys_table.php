<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onUpdate('cascade')->onDelete('cascade');
            $table->string('title');
            $table->text('note');
            $table->text('greeting')->default('こんにちは、これはデフォルトのアンケートです');
            $table->string('greeting_voice_file')->default(config('app.default_greeting_file_name'));
            $table->string('voice_name');
            $table->foreignId('success_ending_id')->nullable()->constrained('endings')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
