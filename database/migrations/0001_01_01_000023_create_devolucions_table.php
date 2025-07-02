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
     Schema::create('devolucions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('venta_id');
        $table->unsignedBigInteger('user_id');
        $table->string('motivo', 255)->nullable();
        $table->timestamps();

        $table->foreign('venta_id')->references('id')->on('ventas')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucions');
    }
};
