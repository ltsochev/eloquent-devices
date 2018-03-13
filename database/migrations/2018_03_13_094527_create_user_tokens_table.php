<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateUserTokensTable extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = config('auth.token_table', 'user_tokens');

        Schema::create($tableName, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->string('remember_token', 255)->nullable()->index();
            $table->string('ip_address', 25)->nullable();
            $table->text('user_agent')->nullable();
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
        $tableName = config('auth.token_table', 'user_tokens');

        Schema::dropIfExists($tableName);
    }
}
