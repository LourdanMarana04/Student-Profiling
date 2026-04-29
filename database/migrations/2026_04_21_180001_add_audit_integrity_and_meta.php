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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->string('previous_hash')->nullable()->after('new_values');
            $table->string('record_hash')->nullable()->after('previous_hash');
            $table->string('ip_address')->nullable()->after('record_hash');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->string('request_path')->nullable()->after('user_agent');
            $table->json('context')->nullable()->after('request_path');

            $table->index(['record_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex(['record_hash']);
            $table->dropColumn(['previous_hash', 'record_hash', 'ip_address', 'user_agent', 'request_path', 'context']);
        });
    }
};
