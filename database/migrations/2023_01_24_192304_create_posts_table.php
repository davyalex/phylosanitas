<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            // $table->string('code')->unique()->nullable();
            $table->string('title')->nullable();
            $table->string('slug')->nullable();
            $table->string('tag')->nullable();
            $table->string('published')->nullable();
            $table->string('lien')->nullable();
            $table->longText('description')->nullable();
            
            $table->foreignId('category_id')
            ->nullable()
            ->constrained('categories')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreignId('user_id')
            ->nullable()
            ->constrained('users')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
