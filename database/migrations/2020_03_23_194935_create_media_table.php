<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->morphs('model');
            $table->uuid('uuid')->nullable();
            $table->string('collection_name')->index();
            $table->string('name')->index();
            $table->string('file_name')->index();
            $table->string('mime_type')->index()->nullable();
            $table->string('disk')->index();
            $table->string('conversions_disk')->index()->nullable();
            $table->unsignedBigInteger('size')->index();
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable();

            $table->nullableTimestamps();
        });
    }

    public function down()
    {
        Schema::drop('media');
    }
}
