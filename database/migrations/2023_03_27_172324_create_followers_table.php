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
        Schema::create('followers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_by')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('follow_to')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->boolean('is_accepted')->default(0)->comment('0 = waitng to follow , 1 = accepted');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('followers');
    }
};
