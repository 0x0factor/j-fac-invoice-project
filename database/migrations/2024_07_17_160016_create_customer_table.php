<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomersTable extends Migration
{
    public function up()
    {
        Schema::create('customer', function (Blueprint $table) {
            $table->increments('CST_ID');
            $table->string('NAME', 30);
            $table->string('NAME_KANA', 100)->nullable();
            $table->string('POSTCODE1', 3)->nullable();
            $table->string('POSTCODE2', 4)->nullable();
            $table->unsignedInteger('CNT_ID')->nullable();
            $table->string('ADDRESS', 50)->nullable();
            $table->string('BUILDING', 50)->nullable();
            $table->string('PHONE_NO1', 20)->nullable();
            $table->string('PHONE_NO2', 20)->nullable();
            $table->string('PHONE_NO3', 20)->nullable();
            $table->string('FAX_NO1', 20)->nullable();
            $table->string('FAX_NO2', 20)->nullable();
            $table->string('FAX_NO3', 20)->nullable();
            $table->string('HONOR_CODE', 4)->nullable();
            $table->string('HONOR_TITLE', 4)->nullable();
            $table->string('WEBSITE', 100)->nullable();
            $table->string('CHR_NAME', 100)->nullable();
            $table->unsignedInteger('CHR_ID')->nullable();
            $table->unsignedInteger('CUTOFF_SELECT')->nullable();
            $table->date('CUTOFF_DATE')->nullable();
            $table->unsignedInteger('PAYMENT_MONTH')->nullable();
            $table->unsignedInteger('PAYMENT_SELECT')->nullable();
            $table->unsignedInteger('PAYMENT_DAY')->nullable();
            $table->string('EXCISE', 50)->nullable();
            $table->string('TAX_FRACTION', 50)->nullable();
            $table->string('TAX_FRACTION_TIMING', 50)->nullable();
            $table->string('FRACTION', 50)->nullable();
            $table->text('NOTE')->nullable();
            $table->unsignedInteger('USR_ID');
            $table->unsignedInteger('UPDATE_USR_ID')->nullable();
            $table->string('SEARCH_ADDRESS', 100)->nullable();
            $table->unsignedInteger('CMP_ID');
            $table->timestamp('INSERT_DATE')->useCurrent();
            $table->timestamp('LAST_UPDATE')->useCurrent();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customer');
    }
}
