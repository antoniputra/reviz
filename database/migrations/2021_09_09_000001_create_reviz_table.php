<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRevizTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviz', function (Blueprint $table) {
            $table->id();
            $table->string('revizable_type')->index();
            $table->unsignedBigInteger('revizable_id')->index();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('funnel')->index()->nullable();
            $table->text('funnel_detail')->nullable();
            $table->unsignedBigInteger('batch')->default(0);
            $table->boolean('is_rollbacked')->default(0);
            $table->timestamp('created_at')->nullable();
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
        Schema::dropIfExists('reviz');
    }
}
