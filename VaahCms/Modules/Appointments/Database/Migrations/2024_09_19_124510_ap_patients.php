<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApPatients extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('ap_patients')) {
            Schema::create('ap_patients', function (Blueprint $table) {
                $table->id()->index();
                $table->string('name',20);
                $table->string('email',50)->unique();
                $table->unsignedBigInteger('phone');
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
        Schema::dropIfExists('ap_patients');
    }
}
