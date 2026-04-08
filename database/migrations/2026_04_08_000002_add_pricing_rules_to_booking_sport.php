<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_sport', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_sport', 'pricing_rules')) {
                $table->json('pricing_rules')->nullable()->after('additional_charges');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_sport', function (Blueprint $table) {
            $table->dropColumn('pricing_rules');
        });
    }
};
