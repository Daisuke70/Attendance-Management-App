<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('attendance_id')->constrained('attendances')->onDelete('cascade');
            $table->time('new_clock_in')->nullable();
            $table->time('new_clock_out')->nullable();
            $table->text('new_note')->nullable();
            $table->string('status', 20);
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
}
