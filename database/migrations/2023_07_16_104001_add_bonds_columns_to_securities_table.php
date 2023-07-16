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
            $table->boolean('is_bond')->nullable();
            $table->decimal('coupon_percent', 18, 4)->nullable();
            $table->decimal('coupon_value', 18, 4)->nullable();
            $table->decimal('coupon_accumulated', 18, 4)->nullable();
            $table->date('next_coupon_date')->nullable();
            $table->date('maturity_date')->nullable();
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
            $table->dropColumn(
                [
                    'is_bond',
                    'coupon_percent',
                    'coupon_value',
                    'coupon_accumulated',
                    'next_coupon_date',
                    'maturity_date',
                ]
            );
        });
    }
};
