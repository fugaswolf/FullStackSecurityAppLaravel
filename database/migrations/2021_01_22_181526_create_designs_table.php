<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('team_id')->nullable();
            $table->string('image');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('is_live')->default(false);
            $table->boolean('upload_successful')->default(false);
            $table->string('disk')->default('public');
            $table->timestamps();

            //foreignkey constraint, user_id reference to id column in user table
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designs');
    }
}
