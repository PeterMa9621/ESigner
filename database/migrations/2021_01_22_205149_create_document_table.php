<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('path');
            $table->string('signed_path')->nullable();
            $table->float('width');
            $table->float('height');
            $table->integer('numPages');
            $table->boolean('is_signed');
            $table->bigInteger('signature_position_id')->unsigned();
            $table->boolean('is_deleted');
            $table->timestamps();
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreign('signature_position_id')->references('id')->on('signature_positions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('document');
    }
}
