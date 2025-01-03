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
        Schema::create('cat_developers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->boolean('is_developer')->default(0);
            $table->boolean('is_builder')->default(0);
            $table->boolean('is_owner')->default(0);
            $table->boolean('is_user_owner')->default(0);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_developers');
    }
};
