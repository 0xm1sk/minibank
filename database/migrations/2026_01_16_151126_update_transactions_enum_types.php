<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum values for transaction types
        DB::statement(
            "ALTER TABLE transactions MODIFY COLUMN type ENUM('deposit', 'withdrawal', 'transfer_in', 'transfer_out', 'credit', 'debit') NOT NULL",
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement(
            "ALTER TABLE transactions MODIFY COLUMN type ENUM('credit', 'debit') NOT NULL",
        );
    }
};
