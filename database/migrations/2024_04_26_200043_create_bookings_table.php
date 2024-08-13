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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()->cascadeOnDelete()->cascadeOnUpdate();

            $table->foreignId('trip_id')->constrained();



            $table->string('payment_status');

            $table->string('reservation_status')->nullable();


            $table->bigInteger('rate')->nullable();


            $table->string('comment')->nullable();


            $table->string('booking_price')->default(0);





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};
