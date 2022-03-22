<?php
/* 
 * Migrations generated by: Skipper (http://www.skipper18.com)
 * Migration id: 9b02fddc-2926-440a-95c2-2de09b69a137
 * Migration local datetime: 2022-03-22 13:06:09.281088
 * Migration UTC datetime: 2022-03-22 12:06:09.281088
 */ 

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SkipperMigrationsSaasRegisterLogin2022032213060928 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('tax');
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
            $table->decimal('tax', 20, 2)->after('vat_id');
        });
    }
}
