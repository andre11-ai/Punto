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
    Schema::create('devolucion_detalles', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('devolucion_id');
        $table->unsignedBigInteger('producto_id');
        $table->integer('cantidad');
        $table->decimal('precio', 10, 2);
        $table->timestamps();

        $table->foreign('devolucion_id')->references('id')->on('devolucions')->onDelete('cascade');
        $table->foreign('producto_id')->references('id')->on('productos');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucion_detalles');
    }
};
