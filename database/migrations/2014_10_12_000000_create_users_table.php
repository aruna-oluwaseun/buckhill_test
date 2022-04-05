<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("users", function (Blueprint $table) {
            $table->id();
            $table->string("uuid");
            $table->string("first_name");
            $table->string("last_name");
            $table->string("avatar")->nullable();
            $table->string("address");
            $table->string("phone_number");
            $table->boolean("is_admin")->default(0);
            $table->boolean("is_marketing")->default(0);
            $table->boolean("is_active")->default(0);
            $table->string("email")->unique();
            $table->timestamp("email_verified_at")->nullable();
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->timestamp("last_login_at")->nullable();
            $table->string("password");
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("users");
    }
}
