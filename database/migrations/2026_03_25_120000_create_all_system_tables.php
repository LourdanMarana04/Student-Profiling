<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * This migration originally duplicated the individual table migrations
     * in this project and caused "table already exists" failures.
     * It is intentionally left as a no-op for existing and fresh installs.
     */
    public function up(): void
    {
        //
    }

    public function down(): void
    {
        //
    }
};
