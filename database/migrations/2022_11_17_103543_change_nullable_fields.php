<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('psp_id', 64)->nullable(true)->change();
            $table->string('psp_instance')->nullable(true)->change();
            $table->string('billing_address', 255)->nullable(true)->change();
            $table->string('billing_city', 255)->nullable(true)->change();
            $table->string('billing_postal_code', 25)->nullable(true)->change();
            $table->string('billing_country', 2)->nullable(true)->change();
            $table->string('phone_prefix', 5)->nullable(true)->change();
            $table->string('phone_number', 50)->nullable(true)->change();
            $table->string('currency', 3)->nullable(true)->change();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teams', function (Blueprint $table) {
          //
        });
    }
};
