<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CretaeModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('models', function (Blueprint $table) {
            $table->integer("id")->autoIncrement();
            $table->integer("schema")->nullable(false);
            $table->string("model")->nullable(false);
            $table->foreign("schema")->references("schema")->on("schemas")->cascadeOnDelete();
            $table->index("schema");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('models');
    }
}
