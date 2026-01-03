<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Chỉ add nếu CHƯA tồn tại
            if (!Schema::hasColumn('tasks', 'is_template')) {
                $table->boolean('is_template')->default(false)->after('id');
            }

            if (!Schema::hasColumn('tasks', 'series_id')) {
                $table->char('series_id', 36)->nullable()->after('is_template');
            }

            if (!Schema::hasColumn('tasks', 'recurrence')) {
                $table->enum('recurrence', ['none', 'daily', 'weekly'])->default('none')->after('series_id');
            }

            if (!Schema::hasColumn('tasks', 'recurrence_until')) {
                $table->date('recurrence_until')->nullable()->after('recurrence');
            }

            if (!Schema::hasColumn('tasks', 'last_generated_for')) {
    $table->date('last_generated_for')->nullable()->after('recurrence_until');
}
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            // Drop theo kiểu “nếu có”
            $cols = [];

            foreach (['last_generated_for','recurrence_until','recurrence','series_id','is_template'] as $c) {
                if (Schema::hasColumn('tasks', $c)) $cols[] = $c;
            }

            if (!empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
