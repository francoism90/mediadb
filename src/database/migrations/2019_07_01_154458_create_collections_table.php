<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->index();
            $table->bigInteger('user_id')->index();
            $table->longText('description')->nullable();
            $table->json('custom_properties')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('collections');
    }
}
