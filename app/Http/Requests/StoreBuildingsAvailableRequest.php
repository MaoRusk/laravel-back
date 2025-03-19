<?php

namespace App\Http\Requests;

use App\Models\Building;
use App\Models\BuildingAvailable;
use Illuminate\Foundation\Http\FormRequest;

class StoreBuildingsAvailableRequest extends FormRequest
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
            'broker_id' => 'required|integer|exists:cat_brokers,id',
            'building_available_id' => 'nullable|integer|exists:buildings_available,id',
            'avl_building_dimensions_ft' => 'required|string|max:45',
            'avl_minimum_space_sf' => 'nullable|integer|min:0',
            'dock_doors' => 'nullable|integer|min:0',
            'rams' => 'nullable|integer|min:0',
            'truck_court_ft' => 'nullable|integer|min:0',
            'shared_truck' => 'nullable|boolean',
            'new_construction' => 'nullable|boolean',
            'is_starting_construction' => 'nullable|boolean',
            'bay_size' => 'nullable|string|max:45',
            'avl_date' => 'nullable|date',
            'parking_space' => 'nullable|integer|min:0',
            'avl_min_lease' => 'required|numeric|min:0',
            'avl_max_lease' => 'required|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'avl_building_phase' => 'required|in:Construction,Planned,Sublease,Expiration,Inventory',
            'status' => 'in:Active,Inactive,Draft',
            'trailer_parking_space' => 'nullable|integer|min:0',
            'fire_protection_system' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['Hose Station', 'Sprinkler', 'Extinguisher'];

                    foreach ($value as $item) {
                        if (!in_array($item, $allowedValues)) {
                            return $fail(__('Invalid value in fire_protection_system.'));
                        }
                    }
                }
            ],
            'above_market_tis' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $allowedValues = ['HVAC', 'CRANE', 'Rail Spur', 'Sprinklers', 'Crossdock', 'Office', 'Leed', 'Land Expansion'];

                    foreach ($value as $item) {
                        if (!in_array($item, $allowedValues)) {
                            return $fail(__('Invalid value in above_market_tis.'));
                        }
                    }
                }
            ],
            'sqftToM2' => 'boolean',
            'yrToMo' => 'boolean',
            'size_sf' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $buildingId = $this->route('building')->id ?? null;

                    if (!$buildingId) {
                        return $fail(__('Building ID is required.'));
                    }
                    $building = Building::find($buildingId);

                    if (!$building) {
                        return $fail(__('Building does not exist.'));
                    }

                    if ($value > $building->building_size_sf) {
                        return $fail(__('The size_sf of buildings_available must be less than or equal to the building_size_sf of buildings.'));
                    }

                    $totalAbsorbed = BuildingAvailable::where('building_id', $buildingId)->sum('size_sf');

                    if (($totalAbsorbed + $value) > $building->building_size_sf) {
                        return $fail(__('The total sum of size_sf for all availability records must be less than or equal to the building_size_sf of buildings.'));
                    }
                },
            ],
        ];
    }
}
