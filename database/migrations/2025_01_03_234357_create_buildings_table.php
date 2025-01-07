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
            $table->foreignId('sub_market_id')->constrained('cat_submarkets');
            $table->foreignId('builder_id')->constrained('building_contacts');
            $table->foreignId('industrial_park_id')->constrained('industrial_parks');
            $table->foreignId('developer_id')->constrained('building_contacts');
            $table->foreignId('owner_id')->constrained('building_contacts');
            $table->foreignId('user_owner_id')->constrained('building_contacts');
            $table->foreignId('contact_id')->constrained('building_contacts');
            $table->string('building_name', 255);
            $table->integer('building_size_sf');
            $table->string('latitud', 45);
            $table->string('longitud', 45);
            $table->integer('year_built')->nullable();
            $table->integer('clear_height')->nullable();
            $table->decimal('total_land', 18, 0)->nullable();
            $table->integer('offices_space')->nullable();
            $table->boolean('has_expansion_land');
            $table->boolean('has_crane');
            $table->boolean('has_hvac');
            $table->boolean('has_rail_spur');
            $table->boolean('has_sprinklers');
            $table->boolean('has_office');
            $table->boolean('has_leed');
            $table->boolean('new_construction');
            $table->boolean('starting_construction');
            $table->string('hvac_production_area', 45)->nullable();
            $table->string('ventilation', 45)->nullable();
            $table->string('transformer_capacity', 45)->nullable();
            $table->string('construction_state', 45)->nullable();
            $table->string('roof_system', 45)->nullable();
            $table->string('skylights_sf', 45)->nullable();
            $table->string('coverage', 45)->nullable();
            $table->string('kvas', 45)->nullable();
            $table->enum('class', ['A', 'B', 'C']);
            $table->enum('building_phase', ['BTS', 'Expansion', 'Inventory', 'Construction', 'Planned', 'Sublease', 'Expiration']);
            $table->enum('type_generation', ['1st Generation', '2nd Generation']);
            $table->enum('currency', ['USD', 'MXP']);
            $table->enum('tenancy', ['Single', 'Multitenant']);
            $table->enum('construction_type', ['TILT_UP', 'Precast', 'Block & Sheet Metal', 'Sheet Metal'])->nullable();
            $table->enum('lightning', ['LED 350 LUXES', 'T5', 'Metal Halide'])->nullable();
            $table->enum('fire_protection_system', ['Hose Station', 'Sprinkler', 'Extinguisher']);
            $table->enum('deal', ['Sale', 'Lease']);
            $table->enum('loading_door', ['Crossdock', 'Back Loading', 'Front Loading'])->nullable();
            $table->enum('above_market_tis', ['HVAC', 'CRANE', 'Rail Spur', 'Sprinklers', 'Crossdock', 'Office', 'Leed', 'Land Expansion'])->nullable();
            $table->enum('status', ['Active', 'Inactive', 'Pending', 'Approved']);
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
