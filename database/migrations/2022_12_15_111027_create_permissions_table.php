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
            ['type' => 'C', 'permissions' => 0],
            ['type' => 'EC', 'permissions' => 3],
            ['type' => 'ED', 'permissions' => 5],
            ['type' => 'EM', 'permissions' => 57],
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
