<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('booking_venuereview', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_venuereview', 'owner_reply')) {
                $table->text('owner_reply')->nullable()->after('would_recommend');
                $table->timestamp('owner_replied_at')->nullable()->after('owner_reply');
            }
        });
    }

    public function down(): void
    {
        Schema::table('booking_venuereview', function (Blueprint $table) {
            $table->dropColumn(['owner_reply', 'owner_replied_at']);
        });
    }
};
