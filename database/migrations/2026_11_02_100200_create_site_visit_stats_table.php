<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Une ligne par jour : visites (1 par session) et pages vues
        Schema::create('site_visit_stats', function (Blueprint $table) {
            $table->date('date')->primary();
            $table->unsignedInteger('visites')->default(0);
            $table->unsignedInteger('pages_vues')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_visit_stats');
    }
};
