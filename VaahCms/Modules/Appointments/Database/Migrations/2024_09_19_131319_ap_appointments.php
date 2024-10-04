<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ApAppointments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if (!Schema::hasTable('ap_appointments')) {
            Schema::create('ap_appointments', function (Blueprint $table) {
                $table->id()->index();
                $table->unsignedTinyInteger('doctor_id')->nullable();
                $table->unsignedTinyInteger('patient_id')->nullable();
                $table->dateTime('date_time')->nullable();
                $table->tinyInteger('status')->nullable();
                $table->string('reason')->nullable();
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
        Schema::dropIfExists('ap_appointments');
    }
}
