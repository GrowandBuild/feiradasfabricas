<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('webhook_events')) {
            Schema::create('webhook_events', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('provider')->index();
                $table->string('event_id')->index();
                $table->json('payload')->nullable();
                $table->timestamp('received_at')->nullable();
                $table->timestamps();

                $table->unique(['provider', 'event_id'], 'webhook_provider_event_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('webhook_events');
    }
};
