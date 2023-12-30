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
        Schema::create('soumissions', function (Blueprint $table) {
            $table->id();

            $table->string('user_session');
            
            $table->foreignId('post_id')
            ->nullable()
            ->constrained('posts')
            ->onUpdate('cascade')
            ->onDelete('set null');

            $table->foreignId('option_sondage_id')
            ->nullable()
            ->constrained('option_sondages')
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
        Schema::dropIfExists('soumissions');
    }
};
