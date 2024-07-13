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
         Schema::create('trips', function (Blueprint $table) {
             $table->id();

             $table->string('flight_name');
             $table->string('location');
             $table->date('trip_start_date');
             $table->date('trip_end_date');
             $table->integer('trip_capacity');
             $table->integer('seats_available')->nullable();
             $table->integer('price_non_optional_activities')->default(0);
             $table->integer('rates')->default(0);
             $table->text('comments')->nullable();
             $table->foreignId('admin_id')
                 ->constrained()->cascadeOnDelete()->cascadeOnUpdate();
             $table->timestamps();
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
