<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInYandexWorkRules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yandex_work_rules', function (Blueprint $table) {
//            $table->unsignedBigInteger('key_id')->nullable();
//            $table->foreign('key_id')->references('id')->on('region_keys')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yandex_work_rules', function (Blueprint $table) {
            //
        });
    }
}
