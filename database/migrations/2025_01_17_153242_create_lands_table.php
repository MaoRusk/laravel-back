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
        Schema::create('lands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('region_id')->constrained('cat_regions', 'id');
            $table->foreignId('market_id')->constrained('cat_markets', 'id');
            $table->foreignId('sub_market_id')->constrained('cat_sub_markets', 'id');
            $table->foreignId('industrial_park_id')->constrained('industrial_parks', 'id');
            $table->foreignId('developer_id')->constrained('cat_developers', 'id');
            $table->foreignId('owner_id')->constrained('cat_developers', 'id');
            $table->foreignId('contact_id')->nullable()->constrained('contacts', 'id');
            $table->string('land_name', 255);
            $table->enum('currency', ['USD', 'MXP']);
            $table->string('latitud', 45);
            $table->string('longitud', 45);
            $table->bigInteger('size_ha');
            $table->string('kvas', 20)->nullable();
            $table->enum('zoning', ['Industrial','Commercial','Residential']);
            $table->enum('parcel_shape', ['Regular', 'Irregular']);
            $table->enum('status', ['Enabled', 'Disabled', 'Pending', 'Approved']);
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
        Schema::dropIfExists('lands');
    }
};
