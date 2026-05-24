<?php

use App\Models\Tenant;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('residences', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Tenant::class)->constrained('tenants');
            $table->date('at')->nullable();
            $table->string('lookup_code')->nullable();
            $table->string('status_id')->nullable();
            $table->json('addition_information')->nullable();
            $table->string('files')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('residences');
    }
};
