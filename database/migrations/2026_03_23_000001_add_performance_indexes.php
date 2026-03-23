<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->index('student_id');
            $table->index('submitted_at');
        });

        Schema::table('grades', function (Blueprint $table): void {
            $table->index('released_at');
        });

        Schema::table('enrollments', function (Blueprint $table): void {
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table): void {
            $table->dropIndex(['student_id']);
            $table->dropIndex(['submitted_at']);
        });

        Schema::table('grades', function (Blueprint $table): void {
            $table->dropIndex(['released_at']);
        });

        Schema::table('enrollments', function (Blueprint $table): void {
            $table->dropIndex(['user_id', 'status']);
        });
    }
};
