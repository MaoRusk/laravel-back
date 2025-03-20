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
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('cat_regions');
            $table->foreignId('market_id')->constrained('cat_markets');
            $table->foreignId('sub_market_id')->constrained('cat_sub_markets');
            $table->foreignId('builder_id')->constrained('cat_developers');
            $table->foreignId('industrial_park_id')->constrained('industrial_parks');
            $table->foreignId('developer_id')->constrained('cat_developers');
            $table->foreignId('owner_id')->constrained('cat_developers');
            $table->foreignId('building_id')->nullable()->constrained('buildings', 'id');
            $table->string('building_name', 255);
            $table->integer('building_size_sf');
            $table->string('latitud', 45);
            $table->string('longitud', 45);
            $table->integer('year_built')->nullable();
            $table->integer('clear_height_ft')->nullable();
            $table->integer('total_land_sf')->nullable();
            $table->integer('offices_space_sf')->nullable();
            $table->boolean('has_crane');
            $table->boolean('has_rail_spur');
            $table->boolean('has_leed');
            $table->string('hvac_production_area', 45)->nullable();
            $table->string('ventilation', 45)->nullable();
            $table->string('roof_system', 45)->nullable();
            $table->string('skylights_sf', 45)->nullable();
            $table->string('coverage', 45)->nullable();
            $table->string('kvas', 20)->nullable();
            $table->integer('expansion_land');
            $table->string('columns_spacing_ft', 20);
            $table->string('bay_size', 20);
            $table->integer('floor_thickness_in');
            $table->string('floor_resistance', 255);
            $table->integer('expansion_up_to_sf');
            $table->enum('class', ['A', 'B', 'C']);
            $table->enum('type_generation', ['1st Generation', '2nd Generation']);
            $table->enum('currency', ['USD', 'MXP']);
            $table->enum('tenancy', ['Single', 'Multitenant']);
            $table->enum('construction_type', ['TILT_UP', 'Precast', 'Block & Sheet Metal', 'Sheet Metal'])->nullable();
            $table->enum('lightning', ['LED', 'T5', 'Metal Halide'])->nullable();
            $table->enum('deal', ['Sale', 'Lease']);
            $table->enum('loading_door', ['Crossdock', 'Back Loading', 'Front Loading'])->nullable();
            $table->enum('status', ['Enabled', 'Disabled', 'Pending', 'Draft']);
            $table->set('fire_protection_system', ["Hose Station", "Sprinkler", "Extinguisher"]);
            $table->set('above_market_tis', ["HVAC", "CRANE", "Rail Spur", "Sprinklers", "Crossdock", "Office", "Leed", "Land Expansion"])->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
