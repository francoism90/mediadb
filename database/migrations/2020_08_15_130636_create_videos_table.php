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
            $table->string('prefixed_id')->nullable()->unique();
            $table->morphs('model');
            $table->json('name');
            $table->json('slug');
            $table->json('overview')->nullable();
            $table->string('status')->index()->nullable();
            $table->string('type')->index()->nullable();
            $table->string('season_number')->index()->nullable();
            $table->string('episode_number')->index()->nullable();
            $table->timestamp('release_date')->index()->nullable();
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
