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
        Schema::create('over_time_requests', function (Blueprint $table) {
            $table->id();
            $table->string('EMPLOYEEID');
            $table->string('DATE');
            $table->string('OTSTART');
            $table->string('OTEND');
            $table->string('OTREASON');
            $table->string('OTSTATUS');
            $table->string('OTREMARKS');
            $table->string('OTAPPROVER');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('over_time_requests');
    }
};
