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
        Schema::create('securities', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('stock_market');
            $table->string('ticker');
            $table->string('short_name')->nullable();
            $table->string('name')->nullable();
            $table->string('lat_name')->nullable();
            $table->decimal('price', 18, 4);
            $table->string('currency');
            $table->integer('lot_size');
            $table->string('isin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('securities');
    }
};
