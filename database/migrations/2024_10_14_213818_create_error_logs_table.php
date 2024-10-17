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
        Schema::create('error_log', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('message')->nullable();
            $table->string('code')->nullable();
            $table->string('url');
            $table->string('method');
            $table->string('file')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('error_logs');
    }
};
