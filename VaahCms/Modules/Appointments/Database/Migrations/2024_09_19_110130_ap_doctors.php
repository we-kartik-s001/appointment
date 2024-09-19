<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApDoctors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('ap_doctors')) {
            Schema::create('ap_doctors', function (Blueprint $table) {
                $table->id()->index();
                $table->string('name',20);
                $table->string('email',50)->unique();
                $table->unsignedBigInteger('phone');
                $table->string('specialization',50);
                $table->timestamp('start_time');
                $table->timestamp('end_time');
                $table->timestamps();
                $table->softDeletes();
            });
        }

    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('ap_doctors');
    }
}
