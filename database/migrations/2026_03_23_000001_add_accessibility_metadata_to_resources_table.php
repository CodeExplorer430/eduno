<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->text('accessibility_notes')->nullable()->after('visibility');
            $table->unsignedSmallInteger('reading_time_minutes')->nullable()->after('accessibility_notes');
        });
    }

    public function down(): void
    {
        Schema::table('resources', function (Blueprint $table) {
            $table->dropColumn(['accessibility_notes', 'reading_time_minutes']);
        });
    }
};
