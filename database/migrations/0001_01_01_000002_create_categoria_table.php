<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

public function up(): void
{
    Schema::create('categorias', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('upc', 10)->nullable();
        $table->unsignedBigInteger('proveedor_id')->nullable();
        $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
