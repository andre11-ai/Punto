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
        Schema::table('ventas', function (Blueprint $table) {
            $table->decimal('pago_recibido', 10, 2)->nullable()->after('total');
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'adeudo'])->default('efectivo')->after('pago_recibido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['pago_recibido', 'metodo_pago']);
        });
    }
};
