<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'region_id' => 'required|integer|exists:cat_regions,id',
            'market_id' => 'required|integer|exists:cat_markets,id',
            'sub_market_id' => 'required|integer|exists:cat_submarkets,id',
            'builder_id' => 'required|integer|exists:cat_developers,id',
            'industrial_park_id' => 'required|integer|exists:industrial_parks,id',
            'developer_id' => 'required|integer|exists:cat_developers,id',
            'owner_id' => 'required|integer|exists:cat_developers,id',
            'user_owner_id' => 'required|integer|exists:cat_developers,id',
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'building_name' => 'required|string|max:255',
            'building_size_sf' => 'required|integer',
            'latitud' => 'required|string|max:45',
            'longitud' => 'required|string|max:45',
            'year_built' => 'nullable|integer',
            'clear_height' => 'nullable|integer',
            'total_land' => 'nullable|numeric|min:0|max:999999999999999999',
            'offices_space' => 'nullable|integer',
            'has_expansion_land' => 'required|boolean',
            'has_crane' => 'required|boolean',
            'has_hvac' => 'required|boolean',
            'has_rail_spur' => 'required|boolean',
            'has_sprinklers' => 'required|boolean',
            'has_office' => 'required|boolean',
            'has_leed' => 'required|boolean',
            'hvac_production_area' => 'nullable|string|max:45',
            'ventilation' => 'nullable|string|max:45',
            'transformer_capacity' => 'nullable|string|max:45',
            'construction_state' => 'nullable|string|max:45',
            'roof_system' => 'nullable|string|max:45',
            'skylights_sf' => 'nullable|string|max:45',
            'coverage' => 'nullable|string|max:45',
            'kvas' => 'nullable|string|max:45',
            'expansion_land' => 'integer',
            'class' => 'required|in:A,B,C',
            'type_generation' => 'required|in:1st Generation,2nd Generation',
            'currency' => 'required|in:USD,MXP',
            'tenancy' => 'required|in:Single,Multitenant',
            'construction_type' => 'nullable|in:TILT_UP,Precast,Block & Sheet Metal,Sheet Metal',
            'lightning' => 'nullable|in:LED 350 LUXES,T5,Metal Halide',
            'fire_protection_system' => 'required|in:Hose Station,Sprinkler,Extinguisher',
            'deal' => 'required|in:Sale,Lease',
            'loading_door' => 'nullable|in:Crossdock,Back Loading,Front Loading',
            'above_market_tis' => 'nullable|in:HVAC,CRANE,Rail Spur,Sprinklers,Crossdock,Office,Leed,Land Expansion',
            'status' => 'required|in:Active,Inactive,Pending,Approved',
        ];

    }
}
