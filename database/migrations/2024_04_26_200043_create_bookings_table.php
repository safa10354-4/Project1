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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('trip_id')
                ->constrained()->cascadeOnDelete()->cascadeOnUpdate();



            $table->string('payment_status');

            $table->string('reservation_status');


            $table->bigInteger('rate');


            $table->string('comment');





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
