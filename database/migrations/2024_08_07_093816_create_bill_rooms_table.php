<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_rooms', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('at');
            $table->string('old_electric');
            $table->string('new_electric');
            $table->string('old_water');
            $table->string('new_water');
            $table->string('price_water');
            $table->string('price_electric');
            $table->string('price_room');
            $table->string('price_garbage');
            $table->foreignIdFor(\App\Models\Room::class)->constrained();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_rooms');
    }
};
