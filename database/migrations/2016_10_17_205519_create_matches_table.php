<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->integer('subject1_id')->unsigned();
            $table->integer('subject2_id')->unsigned();
            $table->integer('times_matched')->unsigned()->default(0);
            $table->timestamp('last_matched_at');
            $table->integer('subject1_wins')->unsigned()->default(0);
            $table->integer('subject2_wins')->unsigned()->default(0);

            $table->foreign('subject1_id')->references('id')->on('subjects')->onDelete('cascade');
            $table->foreign('subject2_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
