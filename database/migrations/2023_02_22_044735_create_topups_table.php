<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->string('nota')->unique();
            $table->foreignId('pengirim')->on((new User)->getTable())->restrictOnDelete()->cascadeOnUpdate();
            $table->foreignId('penerima')->on((new User)->getTable())->restrictOnDelete()->cascadeOnUpdate();
            $table->unsignedInteger('nominal');
            $table->timestamp('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topups');
    }
};
