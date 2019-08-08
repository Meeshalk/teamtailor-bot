<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('link_hash', 129)->unique();
            $table->string('title');
            $table->text('link');
            $table->string('contact_person')->nullable();
            $table->text('contact_email')->nullable();
            $table->string('contact_tel', 15)->nullable();
            $table->integer('jobable_id');
            $table->string('jobable_type');
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
        Schema::dropIfExists('jobs');
    }
}
