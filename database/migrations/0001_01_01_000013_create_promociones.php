<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
{
    Schema::create('promociones', function (Blueprint $table) {
        $table->id();
        $table->string('tipo');
        $table->unsignedBigInteger('id_categoria')->nullable();
        $table->unsignedBigInteger('id_proveedor')->nullable();
        $table->unsignedBigInteger('id_producto')->nullable();
        $table->date('fecha_inicio')->nullable();
        $table->date('fecha_fin')->nullable();
        $table->timestamps();

        $table->foreign('id_categoria')->references('id')->on('categorias')->onDelete('set null');
        $table->foreign('id_proveedor')->references('id')->on('proveedores')->onDelete('set null');
        $table->foreign('id_producto')->references('id')->on('productos')->onDelete('set null');
    });
}

    public function down(): void
    {
        Schema::dropIfExists('promociones');
    }
};
