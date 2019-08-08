<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->increments('id');
            $table->string('domain')->unique();
            $table->string('orignal_url')->nullable();
            $table->text('parent_domain')->nullable();
            $table->string('method')->nullable();
            $table->unsignedTinyInteger('redirects')->nullable();
            $table->text('redirected_from')->nullable();
            $table->text('redirected_url')->nullable();
            $table->text('job_url')->nullable();
            $table->boolean('secure')->default(0);
            $table->boolean('verified')->default(0);
            $table->text('job_page')->nullable();
            $table->string('type')->nullable();
            $table->float('completed_in', 6, 2)->default(0.00);
            $table->text('links_checked')->nullable();
            $table->boolean('tested')->default(0);
            $table->boolean('department_filter')->nullable();
            $table->boolean('location_filter')->nullable();
            $table->unsignedInteger('job_count')->nullable();
            $table->integer('domainable_id');
            $table->string('domainable_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('domains');
    }
}
