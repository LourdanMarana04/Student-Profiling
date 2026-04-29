<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key, make column nullable, then re-add foreign key
        DB::statement('ALTER TABLE `students` DROP FOREIGN KEY `students_user_id_foreign`');
        DB::statement('ALTER TABLE `students` MODIFY `user_id` BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE `students` ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE `students` DROP FOREIGN KEY `students_user_id_foreign`');
        DB::statement('ALTER TABLE `students` MODIFY `user_id` BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE `students` ADD CONSTRAINT `students_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE');
    }
};
