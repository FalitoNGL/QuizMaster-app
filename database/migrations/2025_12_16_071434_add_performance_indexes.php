<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Quiz Attempts - frequently queried by user_id, quiz_id, status
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->index(['user_id', 'quiz_id']);
            $table->index(['user_id', 'status']);
            $table->index(['quiz_id', 'status']);
        });

        // Quiz Answers - queried by attempt_id and question_id
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->index('attempt_id');
            $table->index('question_id');
        });

        // Questions - queried by quiz_id
        Schema::table('questions', function (Blueprint $table) {
            $table->index('quiz_id');
        });

        // Options - queried by question_id
        Schema::table('options', function (Blueprint $table) {
            $table->index('question_id');
        });

        // Users - for leaderboard sorting
        Schema::table('users', function (Blueprint $table) {
            $table->index(['xp', 'level']);
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'quiz_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['quiz_id', 'status']);
        });

        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropIndex(['attempt_id']);
            $table->dropIndex(['question_id']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['quiz_id']);
        });

        Schema::table('options', function (Blueprint $table) {
            $table->dropIndex(['question_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['xp', 'level']);
        });
    }
};
