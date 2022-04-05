<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJwtTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("jwt_tokens", function (Blueprint $table) {
            $table->id();
            $table->text("unique_id");
            $table->foreignId("user_id");
            $table->string("token_title")->nullable();
            $table->string("restrictions")->nullable();
            $table->string("permissions")->nullable();
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->timestamp("expires")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("jwt_tokens");
    }
}
