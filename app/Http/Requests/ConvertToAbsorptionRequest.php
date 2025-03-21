<?php

namespace App\Http\Requests;

use App\Models\Building;
use App\Models\BuildingAvailable;
use Illuminate\Foundation\Http\FormRequest;

class ConvertToAbsorptionRequest extends FormRequest
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
            'broker_id' => 'sometimes|integer|exists:cat_brokers,id',
            'dock_doors' => 'sometimes|integer|min:0',
            'ramps' => 'sometimes|integer|min:0',
            'truck_court_ft' => 'sometimes|integer|min:0',
            'shared_truck' => 'sometimes|boolean',
            'new_construction' => 'sometimes|boolean',
            'is_starting_construction' => 'sometimes|boolean',
            'bay_size' => 'sometimes|string|max:45',
            'parking_space' => 'sometimes|integer|min:0',
            'trailer_parking_space' => 'sometimes|integer|min:0',
            'avl_date' => 'sometimes|date',
            'abs_min_lease' => 'required|numeric|min:0',
            'abs_max_lease' => 'required|numeric|min:0',
            'fire_protection_system' => [
                'sometimes',
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
                'sometimes',
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
            'abs_tenant_id' => 'required|integer|exists:cat_tenants,id',
            'abs_industry_id' => 'required|integer|exists:cat_industries,id',
            'abs_country_id' => 'required|integer|exists:countries,id',
            'abs_lease_term_month' => 'required|integer|min:0',
            'abs_closing_rate' => 'required|numeric|min:0',
            'abs_closing_date' => 'required|date',
            'abs_company_type' => 'required|in:Existing Company,New Company in Market,New Company in Mexico',
            'abs_lease_up' => 'required|date',
            'abs_month' => 'required|string',
            'abs_sale_price' => 'required|numeric|min:0',
            'abs_final_use' => 'required|in:Logistic,Manufacturing',
            'abs_type' => 'required|in:BTS,Expansion,Inventory,BTS Expansion',
            'abs_shelter_id' => 'required|exists:cat_shelters,id',
            'size_sf' => [
                'sometimes',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $buildingId = $this->route('building')->id ?? null;
                    $buildingAvailableId = $this->route('buildingAvailable')->id ?? null;

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

                    $totalAbsorbed = BuildingAvailable::where('building_id', $buildingId)
                        ->sum('size_sf');


                    $currentSizeSf = BuildingAvailable::where('id', $buildingAvailableId)->value('size_sf') ?? 0;

                    if (($totalAbsorbed - $currentSizeSf + $value) > $building->building_size_sf) {
                        return $fail(__('The total sum of size_sf for all availability/absorption records must be less than or equal to the building_size_sf of buildings.'));
                    }
                },
            ],
        ];
    }
}
