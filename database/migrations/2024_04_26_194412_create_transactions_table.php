l<?php

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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();


            $table->foreignId('wallet_id')
                ->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->double('amount');

            $table->double('balance_after_transaction')->default(0);


            $table->boolean('type');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};