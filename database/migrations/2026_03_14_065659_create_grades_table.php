<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('graded_by')->constrained('users');
            $table->decimal('score', 7, 2);
            $table->text('feedback')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();

            $table->index('graded_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
