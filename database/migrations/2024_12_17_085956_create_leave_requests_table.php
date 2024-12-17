<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->string('EMPLOYEEID');
            $table->string('LEAVETYPE');
            $table->string('LEAVESTART');
            $table->string('LEAVEEND');
            $table->string('LEAVEREASON');
            $table->string('LEAVESTATUS');
            $table->string('LEAVEREMARKS');
            $table->string('LEAVEAPPROVER');
            $table->string('LEAVECOUNT');
            $table->string('HALFDAY')->nullable();
            $table->string('LINK')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
