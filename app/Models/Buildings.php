<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buildings extends Model
{
    use HasFactory;

    protected $table = 'buildings';

    protected $fillable = [
        'builder_state_id',
        'building_name',
        'class_id',
        'building_size_sf',
        'expansion_land',
        'status_id',
        'industrial_park_id',
        'type_id',
        'owner_id',
        'developer_id',
        'builder_id',
        'region_id',
        'market_id',
        'sub_market_id',
        'deal_id',
        'currency_id',
        'sale_price_usd',
        'tenancy_id',
        'latitud',
        'longitud',
        'year_built',
        'clear_height',
        'offices_space',
        'crane',
        'hvac',
        'rail_spur',
        'sprinklers',
        'office',
        'leed',
        'total_land',
        'hvac_production_area',
        'createdAt',
        'modifiedAt',
        'status',
    ];

    // * Definir columnas de marca de tiempo personalizadas
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'modifiedAt';

    // Relaciones que deberían estar definidas en el modelo Buildings
    public function buildingAvailable()
    {
        return $this->hasOne(BuildingsAvailable::class, 'building_id');
    }

    public function buildingAbsorption()
    {
        return $this->hasOne(BuildingsAbsorption::class, 'building_id');
    }

    public function buildingContacts()
    {
        return $this->hasOne(BuildingsContacts::class, 'building_id');
    }

    public function buildingFeatures()
    {
        return $this->hasOne(BuildingsFeatures::class, 'building_id');
    }

    public function buildingImages()
    {
        return $this->hasMany(BuildingsImages::class, 'buildingId');
    }
}
