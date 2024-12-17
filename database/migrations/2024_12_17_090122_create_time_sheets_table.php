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
        Schema::create('time_sheets', function (Blueprint $table) {
            $table->id();
            $table->string('EMPLOYEEID');
            $table->string('DATE');
            $table->string('TIMEIN');
            $table->string('TIMEOUT');
            $table->string('STATUS');
            $table->string('REASON');
            $table->string('REMARKS');
            $table->string('APPROVER');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_sheets');
    }
};
