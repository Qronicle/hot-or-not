<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('times_shown')->default(0);
            $table->integer('times_won')->default(0);
            $table->integer('times_lost')->default(0);
            $table->float('percentage_won')->default(0);
            $table->timestamp('last_shown_at')->default(null);
            $table->timestamps();
        });
        $defaultSubjects = ['Kim', 'Maarten', 'Maxim', 'Rias', 'Seeger', 'Simon', 'Thomas'];
        foreach ($defaultSubjects as $name) {
            DB::table('subjects')->insert([
                'name' => $name,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
