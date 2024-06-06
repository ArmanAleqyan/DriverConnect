<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments_data', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_login')->nullable();
            $table->string('client_password')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('certificate_path')->nullable();
            $table->string('truststore_path')->nullable();
            $table->string('code')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
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
        Schema::dropIfExists('payments_data');
    }
}
