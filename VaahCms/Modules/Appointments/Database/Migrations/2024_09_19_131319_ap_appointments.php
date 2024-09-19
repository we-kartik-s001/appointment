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
                $table->unsignedTinyInteger('doctor_id');
                $table->unsignedTinyInteger('patient_id');
                $table->timestamp('date');
                $table->timestamp('time');
                $table->tinyInteger('status')->default(1);
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
