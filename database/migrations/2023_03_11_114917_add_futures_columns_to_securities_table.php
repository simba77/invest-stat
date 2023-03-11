<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('securities', function (Blueprint $table) {
            $table->boolean('is_future')->nullable();
            $table->date('expiration')->nullable();
            $table->decimal('step_price', 18, 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('securities', function (Blueprint $table) {
            $table->dropColumn(['is_futures', 'expiration', 'step_price']);
        });
    }
};
