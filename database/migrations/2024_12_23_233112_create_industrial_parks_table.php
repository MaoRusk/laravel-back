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
        Schema::disableForeignKeyConstraints();
        Schema::create('industrial_parks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained('cat_markets');
            $table->foreignId('sub_market_id')->constrained('cat_sub_markets');
            $table->foreignId('owner_id')->nullable()->constrained('cat_developers');
            $table->foreignId('region_id')->nullable()->constrained('cat_regions');
            $table->string('name');
            $table->decimal('total_land_ha', 5, 2)->default(0);
            $table->decimal('available_land_ha', 5,2)->default(0);
            $table->decimal('reserve_land_ha', 5, 2)->default(0);
            $table->unsignedSmallInteger('building_number')->default(0);
            $table->enum('land_condition', ['Fully Developed','Undeveloped'])->nullable();
            $table->enum('park_type', ['Pocket','Industrial Park','Mega Park'])->default('Pocket');
            $table->unsignedSmallInteger('year_built')->default(0);
            $table->boolean('has_rail_spur')->default(false);
            $table->boolean('has_natural_gas')->default(false);
            $table->boolean('has_sewage')->default(false);
            $table->boolean('has_water')->default(false);
            $table->boolean('has_electric')->default(false);
            $table->string('latitude', 20)->nullable();
            $table->string('longitude', 20)->nullable();
            $table->string('comments', 255)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industrial_parks');
    }
};
