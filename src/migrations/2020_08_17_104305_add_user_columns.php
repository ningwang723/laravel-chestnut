<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table("users", function (Blueprint $table) {
            $table->string('phone', 11)->unique()->nullable()->after("email");
            $table->string('openid')->unique()->nullable()->after("phone");
            $table->string('avatar')->nullable();
            $table->string("session_key", 100)->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table("users", function (Blueprint $table) {
            $table->dropColumn(['phone', 'openid', 'avatar', 'session_key']);
            $table->dropSoftDeletes();
        });
    }
}
