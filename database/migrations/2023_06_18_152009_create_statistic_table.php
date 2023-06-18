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
    public function up()
    {
        Schema::create('statistic', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('account_id')->constrained('accounts');
            $table->dateTime('date');
            $table->decimal('balance', 18, 4);
            $table->decimal('usd_balance', 18, 4);
            $table->decimal('deposits', 18, 4);
            $table->decimal('current', 18, 4);
            $table->decimal('profit', 18, 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistic');
    }
};
