<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cleanup_audit', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('table_name')->index();
            $table->unsignedBigInteger('old_id')->nullable()->index();
            $table->unsignedBigInteger('new_id')->nullable()->index();
            $table->string('operation')->nullable();
            $table->text('reason')->nullable();
            $table->text('sql_executed')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cleanup_audit');
    }
};
