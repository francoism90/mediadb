<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateViewsTable extends Migration
{
    /**
     * The database schema.
     *
     * @var \Illuminate\Support\Facades\Schema
     */
    protected $schema;

    /**
     * The table name.
     *
     * @var string
     */
    protected $table;

    /**
     * Create a new migration instance.
     */
    public function __construct()
    {
        $this->schema = Schema::connection(
            config('eloquent-viewable.models.view.connection')
        );

        $this->table = config('eloquent-viewable.models.view.table_name');
    }

    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->schema->create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->morphs('viewable');
            $table->text('visitor')->nullable();
            $table->string('collection')->nullable();
            $table->timestamp('viewed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
