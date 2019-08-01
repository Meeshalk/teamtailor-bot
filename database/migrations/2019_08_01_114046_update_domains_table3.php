<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDomainsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(){
       Schema::table('domains', function (Blueprint $table) {
           $table->boolean('tested')->default(0)->after('type');
           $table->text('links_checked')->nullable()->after('type');
           $table->float('completed_in', 6, 2)->default(0.00)->after('type');
       });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down(){
       Schema::table('domains', function (Blueprint $table) {
           $table->dropColumn('tested');
           $table->dropColumn('links_checked');
           $table->dropColumn('completed_in');
       });
     }
}
