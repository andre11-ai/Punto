<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('rol', ['Super-Admin','Admin','User'])
                  ->default('User');
            $table->enum('turno', ['Matutino', 'Vespertino', 'Mixto'])
                  ->default('Mixto');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::statement('ALTER TABLE users AUTO_INCREMENT = 1110001;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
