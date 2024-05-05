<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnInUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('work_status')->nullable();
            $table->string('created_date')->nullable();
            $table->string('hire_date')->nullable();
            $table->string('is_selfemployed')->nullable();
            $table->string('has_contract_issue')->nullable();
            $table->string('modified_date')->nullable();
            $table->integer('create_account_status')->nullable()->default(0);
            $table->integer('add_car_status')->nullable()->default(0);
            $table->integer('app_or_yandex')->nullable()->default(0)->comment('Если 0 это из приложения если 1 то из яндекса');
            $table->unsignedBigInteger('park_id')->nullable();
            $table->unsignedBigInteger('work_rule_id')->nullable();
            $table->foreign('park_id')->references('id')->on('region_keys')->onDelete('cascade');
            $table->foreign('work_rule_id')->references('id')->on('yandex_work_rules')->onDelete('cascade');

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
            //
        });
    }
}
