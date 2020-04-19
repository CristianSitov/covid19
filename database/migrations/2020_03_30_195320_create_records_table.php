<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->date('capture_date');
            $table->string('country');
            $table->integer('confirmed');
            $table->integer('totals_confirmed')->default(0);
            $table->integer('avg3_confirmed')->default(0);
            $table->integer('avg7_confirmed')->default(0);
            $table->integer('deaths');
            $table->integer('totals_deaths')->default(0);
            $table->integer('avg3_deaths')->default(0);
            $table->integer('avg7_deaths')->default(0);
            $table->integer('population');
            $table->integer('confirmed_million')->default(0);
            $table->integer('deaths_million')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('records');
    }
}
