<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagTables extends Migration
{
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('prefixed_id')->nullable()->unique();
            $table->json('name');
            $table->json('slug');
            $table->json('description')->nullable();
            $table->string('type')->nullable();
            $table->integer('order_column')->nullable();
            $table->schemalessAttributes('extra_attributes');
            $table->timestamps();
        });

        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->morphs('taggable');

            $table->unique(['tag_id', 'taggable_id', 'taggable_type']);
        });
    }

    public function down()
    {
        Schema::drop('taggables');
        Schema::drop('tags');
    }
}
