<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIndustrialParkRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'market_id' => 'required|exists:cat_markets,id',
            'sub_market_id' => 'required|exists:cat_sub_markets,id',
            'owner_id' => 'nullable|integer|exists:cat_developers,id',
            'region_id' => 'nullable|integer|exists:cat_regions,id',
            'total_land_ha' => 'nullable|numeric|min:0|max:999.99',
            'available_land_ha' => 'nullable|numeric|min:0|max:999.99',
            'reserve_land_ha' => 'nullable|numeric|min:0|max:999.99',
            'building_number' => 'nullable|integer|min:0|max:65535',
            'land_condition' => 'nullable|in:Fully Developed,Undeveloped',
            'park_type' => 'in:Pocket,Industrial Park,Mega Park|nullable',
            'year_built' => 'nullable|integer|min:0|max:65535',
            'has_rail_spur' => 'boolean',
            'has_natural_gas' => 'boolean',
            'has_sewage' => 'boolean',
            'has_water' => 'boolean',
            'has_electric' => 'boolean',
            'latitude' => 'nullable|string|max:20',
            'longitude' => 'nullable|string|max:20',
            'comments' => 'nullable|string|max:255',
        ];
    }
}
