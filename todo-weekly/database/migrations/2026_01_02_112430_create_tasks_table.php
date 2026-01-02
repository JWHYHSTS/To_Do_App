<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title', 255);
            $table->text('description')->nullable();

            // Keep as string to be flexible; validate allowed values in requests.
            $table->string('status', 20)->default('To Do');

            // low/medium/high (optional but recommended)
            $table->string('priority', 10)->nullable(); // low|medium|high

            $table->date('scheduled_date');
            $table->time('scheduled_time');

            $table->unsignedSmallInteger('duration_minutes')->default(60);

            $table->dateTime('started_at')->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'scheduled_date']);
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
