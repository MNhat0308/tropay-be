<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_rooms', function (Blueprint $table): void {
            $table->float('electric_consumption')->nullable();
            $table->float('water_consumption')->nullable();
            $table->float('total_price')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('bill_rooms', function (Blueprint $table): void {
            $table->dropColumn('electric_consumption');
            $table->dropColumn('water_consumption');
            $table->dropColumn('total_price');
        });
    }
};
