<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flash_infos', function (Blueprint $table) {
            $table->id();
            $table->string('message', 255);
            $table->string('lien', 500)->nullable();
            $table->enum('niveau', ['info', 'important', 'urgent'])->default('info');
            $table->boolean('actif')->default(true);
            $table->timestamp('debut_at')->nullable();
            $table->timestamp('fin_at')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['actif', 'debut_at', 'fin_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flash_infos');
    }
};
