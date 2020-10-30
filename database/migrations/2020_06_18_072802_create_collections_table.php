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
            $table->json('name');
            $table->json('slug');
            $table->string('type')->nullable()->index();
            $table->json('overview')->nullable();
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
