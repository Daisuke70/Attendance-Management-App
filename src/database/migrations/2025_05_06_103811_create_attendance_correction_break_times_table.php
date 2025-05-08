<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionBreakTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_break_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_correction_request_id');
            $table->time('new_start_time')->nullable();
            $table->time('new_end_time')->nullable();
            $table->timestamps();

            $table->foreign('attendance_correction_request_id', 'acbt_acr_id_foreign')
                ->references('id')->on('attendance_correction_requests')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_correction_break_times');
    }
}
