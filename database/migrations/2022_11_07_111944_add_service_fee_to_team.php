<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 7aa398fd-ab17-4c2e-89f0-00ee178fc18f
 * Migration local datetime: 2022-10-18 11:19:44.808338
 * Migration UTC datetime: 2022-10-18 09:19:44.808338
 */ 

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
            $table->integer('service_fee')->default(1)->unsigned()->after('currency');
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
            $table->dropColumn('service_fee');
        });
    }
};
