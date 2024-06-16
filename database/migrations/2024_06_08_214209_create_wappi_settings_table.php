<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWappiSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wappi_settings', function (Blueprint $table) {
            $table->id();
            $table->string('whatsapp_id')->nullable();
            $table->string('whatsapp_token')->nullable();
            $table->string('telegram_token')->nullable();
            $table->string('telegram_id')->nullable();
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
        Schema::dropIfExists('wappi_settings');
    }
}
