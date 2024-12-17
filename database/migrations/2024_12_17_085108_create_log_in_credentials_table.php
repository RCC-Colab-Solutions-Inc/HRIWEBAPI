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
        Schema::create('log_in_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('EMPLOYEEID')->unique();
            $table->string('USERNAME')->unique();
            $table->string('PASSWORD');
            $table->enum('USERTYPE', ['ADMIN', 'EMPLOYEE']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_in_credentials');
    }
};
