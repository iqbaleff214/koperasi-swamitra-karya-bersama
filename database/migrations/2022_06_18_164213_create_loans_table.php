<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('period')->default(12)->comment('Jangka waktu cicilan dengan satuan bulan. Default 12 bulan.');
            $table->unsignedInteger('amount')->default(0)->comment('jumlah pinjaman');
            $table->unsignedInteger('installment')->default(0)->comment('cicilan');
            $table->unsignedInteger('return_amount')->default(0)->comment('pengembalian');
            $table->foreignId('customer_id')->constrained('customers')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('collateral_id')->constrained('collaterals')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};
