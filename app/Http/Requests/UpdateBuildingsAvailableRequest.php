<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBuildingsAvailableRequest extends FormRequest
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
            'broker_id' => 'required|integer|exists:cat_developers,id',
            'size_sf' => 'required|integer|min:0',
            'avl_building_dimensions_ft' => 'required|string|max:45',
            'avl_minimum_space_sf' => 'nullable|integer|min:0',
            'dock_doors' => 'nullable|integer|min:0',
            'rams' => 'nullable|integer|min:0',
            'truck_court_ft' => 'nullable|integer|min:0',
            'shared_truck' => 'nullable|boolean',
            'new_construction' => 'nullable|boolean',
            'is_starting_construction' => 'nullable|boolean',
            'bay_size' => 'nullable|string|max:45',
            'columns_spacing' => 'nullable|string|max:45',
            'avl_date' => 'nullable|date',
            'parking_space' => 'nullable|integer|min:0',
            'avl_min_lease' => 'required|numeric|min:0',
            'avl_max_lease' => 'required|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'avl_building_phase' => 'required|in:Construction,Planned,Sublease,Expiration,Inventory',
            'trailer_parking_space' => 'nullable|integer|min:0',
            'fire_protection_system' => 'required|in:Hose Station,Sprinkler,Extinguisher',
            'above_market_tis' => 'nullable|in:HVAC,CRANE,Rail Spur,Sprinklers,Crossdock,Office,Leed,Land Expansion',
            'sqftToM2' => 'boolean',
        ];
    }
}
