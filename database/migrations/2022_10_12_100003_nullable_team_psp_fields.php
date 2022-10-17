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
        Schema::table('teams', function (Blueprint $table) {
            Schema::table('teams', function (Blueprint $table) {
                $table->string('psp_id', 64)->nullable(true)->after('id')->change();
            });
            Schema::table('teams', function (Blueprint $table) {
                $table->string('psp_instance')->nullable(true)->after('psp_id')->change();
            });
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
            Schema::table('teams', function (Blueprint $table) {
                $table->string('psp_instance')->after('psp_id')->change();
            });
            Schema::table('teams', function (Blueprint $table) {
                $table->string('psp_id', 64)->after('id')->change();
            });
        });
    }
};
