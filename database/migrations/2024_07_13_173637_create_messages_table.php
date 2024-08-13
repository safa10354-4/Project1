<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')
                ->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type');
            $table->string('sender_name');
            $table->string('sender_image');
            $table->text('message');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};

//            $table->foreignId('conversation_id')->constrained('conversations')->onDelete('cascade');
//            $table->foreignId('sender_id')->constrained('users');
