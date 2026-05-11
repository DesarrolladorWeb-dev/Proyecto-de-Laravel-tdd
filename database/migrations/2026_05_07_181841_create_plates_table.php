<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Restaurant::class)->constrained();
            $table->string('name');
            $table->string('price'); //como no lo vamos a usar solo lo vamos a mostrar solo lo coloco como string , $1,000 (yo quiero que lo pueda guardar asi pero tambien lo piedo hacer 1000 y nosotros mostrarlo  )
            $table->text('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plates');
    }
};
