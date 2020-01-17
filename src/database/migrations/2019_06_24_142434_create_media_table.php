<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug')->unique();
            $table->string('name')->index();
            $table->morphs('model');
            $table->longText('description')->nullable();
            $table->string('file_name')->index();
            $table->string('mime_type')->nullable();
            $table->string('disk')->index();
            $table->string('collection_name')->index();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties')->nullable();
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
