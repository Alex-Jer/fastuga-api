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
        Schema::create('permissions', function (Blueprint $table) {
            $table->enum('type', ['C', 'EC', 'ED', 'EM'])->primary();
            $table->bigInteger('permissions');
        });

        DB::table('permissions')->insert([
            ['type' => 'C', 'permissions' => 0], // 0 = 0000000
            ['type' => 'EC', 'permissions' => 3], // 3 = 0000011
            ['type' => 'ED', 'permissions' => 69], // 69 = 1000101
            ['type' => 'EM', 'permissions' => 57], // 57 = 0111001
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};
