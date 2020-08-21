<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->string('slug')->unique();
            $table->string('name')->index();
            $table->string('type')->index()->nullable();
            $table->string('status')->index()->nullable();
            $table->timestamp('release_date')->index()->nullable();
            $table->string('original_language')->index()->nullable();
            $table->string('original_title')->index()->nullable();
            $table->unsignedInteger('season_number')->index()->nullable();
            $table->unsignedInteger('episode_number')->index()->nullable();
            $table->text('tagline')->nullable();
            $table->longText('overview')->nullable();
            $table->json('custom_properties')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
}
