<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * @return void
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->morphs('model');
            $table->string('slug')->unique();
            $table->string('name')->index();
            $table->string('type')->index()->nullable();
            $table->longText('description')->nullable();
            $table->json('custom_properties')->nullable();
            $table->nullableTimestamps();
        });

        Schema::create('collectables', function (Blueprint $table) {
            $table->unsignedBigInteger('collection_id');
            $table->morphs('collectable');

            $table->unique(['collection_id', 'collectable_id', 'collectable_type'], 'collections_unique');

            $table->foreign('collection_id')
                  ->references('id')
                  ->on('collections')
                  ->onDelete('cascade');
        });
    }

    /**
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('collectables');
        Schema::dropIfExists('collections');
    }
}
