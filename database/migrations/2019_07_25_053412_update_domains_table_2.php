<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDomainsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(){
       Schema::table('domains', function (Blueprint $table) {
           $table->text('parent_domain')->nullable()->after('orignal_url');
       });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down(){
       Schema::table('domains', function (Blueprint $table) {
           $table->dropColumn('parent_domain');
       });
     }
}
