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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->date('birth')->nullable();
            $table->string('last_education')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->unique();
            $table->dateTime('joined_at')->nullable();
            $table->enum('role', ['manager', 'teller', 'collector'])->default('collector');
            $table->string('photo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
