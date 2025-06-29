<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuAndDevolucionToProductosTable extends Migration
{
    public function up()
    {
    Schema::table('productos', function (Blueprint $table) {
        $table->integer('sku')->default(0);
        $table->boolean('devolucion')->default(true);
    });
    }

    public function down()
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('devolucion');
        });
    }
}
