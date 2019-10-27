<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned()->default(null);
            $table->string('email', 100)->unique();
            $table->string('phone', 45)->unique();
            $table->string('password', 100);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->date('birth_date')->nullable();
            $table->string('confirmation_code', 120)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            // $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropForeign(['role_id']);
        // });

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
}
