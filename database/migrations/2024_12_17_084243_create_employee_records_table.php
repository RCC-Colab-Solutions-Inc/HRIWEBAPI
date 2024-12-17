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
        Schema::create('employee_records', function (Blueprint $table) {
            $table->id();
            $table->string('EMPLOYEEID')->unique();
            $table->string('FIRSTNAME');
            $table->string('MIDDLENAME');
            $table->string('LASTNAME');
            $table->string('CONTACTNUMBER');
            $table->string('ADDRESS');
            $table->string('EMAIL')->unique();
            $table->string('SSS');
            $table->string('PAGIBIG');
            $table->string('PHILHEALTH');
            $table->string('TIN');
            $table->string('DATEHIRED');
            $table->string('POSITION');
            $table->string('DEPARTMENT');
            $table->enum('APPROVER', ['0', '1']);
            $table->string('APPROVERID');
            $table->string('IMAGELINK');
            $table->enum('EMPLOYEESTATUS', ['PROBATIONARY','REGULAR','DISCONTINUE EMPLOYMENT','FIRED','RETIRED']);
            $table->enum('DISPLAYRECORD', ['0', '1']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_records');
    }
};
