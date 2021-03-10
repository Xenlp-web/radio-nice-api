<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSocialColumnsToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->bigInteger('vkontakte_id')->nullable(true);
            $table->bigInteger('facebook_id')->nullable(true);
            $table->bigInteger('google_id')->nullable(true);
            $table->bigInteger('yandex_id')->nullable(true);
            $table->bigInteger('mailru_id')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['vkontakte_id', 'facebook_id', 'google_id', 'yandex_id', 'mailru_id']);
        });
    }
}
