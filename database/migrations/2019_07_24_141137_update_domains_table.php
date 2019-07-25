<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up(){
       Schema::table('domains', function (Blueprint $table) {
           $table->string('method')->nullable()->after('orignal_url');
           $table->text('redirected_from')->nullable()->after('redirects');
       });
     }

     /**
      * Reverse the migrations.
      *
      * @return void
      */
     public function down(){
       Schema::table('domains', function (Blueprint $table) {
           $table->dropColumn('method');
           $table->dropColumn('redirected_from');
       });
     }
}
