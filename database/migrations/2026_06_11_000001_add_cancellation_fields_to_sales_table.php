<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status')->default('completed')->after('receipt_path');
            $table->timestamp('canceled_at')->nullable()->after('status');
            $table->foreignId('canceled_by')->nullable()->after('canceled_at')->constrained('users')->nullOnDelete();
            $table->string('cancellation_reason')->nullable()->after('canceled_by');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropConstrainedForeignId('canceled_by');
            $table->dropColumn(['status', 'canceled_at', 'cancellation_reason']);
        });
    }
};
