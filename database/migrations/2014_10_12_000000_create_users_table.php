<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('no_hp')->unique();
            $table->string('nama');
            $table->string('pin');
            $table->enum('tipe', ['A', 'C', 'S', 'B']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
