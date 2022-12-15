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
        Schema::create('scopes', function (Blueprint $table) {
            $table->id();
            $table->string('scope_name', 20);
        });

        DB::table('scopes')->insert([
            ['scope_name' => 'view_orders'],
            ['scope_name' => 'prepare_orders'],
            ['scope_name' => 'deliver_orders'],
            ['scope_name' => 'cancel_orders'],
            ['scope_name' => 'manage_users'],
            ['scope_name' => 'manage_products'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('scopes');
    }
};
