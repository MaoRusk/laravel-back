<?php

namespace App\Http\Requests;

use App\Models\Building;
use App\Models\BuildingAvailable;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBuildingAbsorptionDraftRequest extends FormRequest
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
            'dock_doors' => 'nullable|integer|min:0',
            'abs_tenant_id' => 'required|integer|exists:cat_tenants,id',
            'abs_industry_id' => 'required|integer|exists:cat_industries,id',
            'abs_country_id' => 'required|integer|exists:countries,id',
            'broker_id' => 'required|integer|exists:cat_brokers,id',
            'building_available_id' => 'nullable|integer|exists:buildings_available,id',
            'rams' => 'nullable|integer|min:0',
            'truck_court_ft' => 'nullable|integer|min:0',
            'shared_truck' => 'nullable|boolean',
            'new_construction' => 'nullable|boolean',
            'is_starting_construction' => 'nullable|boolean',
            'bay_size' => 'nullable|string|max:45',
            'abs_lease_term_month' => 'nullable|integer|min:0',
            'parking_space' => 'nullable|integer|min:0',
            'abs_closing_rate' => 'required|numeric|min:0',
            'abs_closing_date' => 'nullable|date',
            'abs_lease_up' => 'nullable|date',
            'abs_month' => 'nullable|date',
            'abs_sale_price' => 'nullable|numeric|min:0',
            'created_by' => 'nullable|integer|exists:users,id',
            'updated_by' => 'nullable|integer|exists:users,id',
            'abs_building_phase' => 'required|in:BTS,Expansion,Inventory',
            'abs_final_use' => 'nullable|in:Logistic,Manufacturing',
            'abs_company_type' => 'nullable|in:Existing Company,New Company in Market,New Company in Mexico',
            'status' => 'in:Enabled,Draft',
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
            'abs_deal' =>'required|in:Sale,Lease',
            'abs_broker_id' => 'nullable|exists:cat_brokers,id',
            'abs_shelter_id' => 'nullable|exists:cat_shelters,id',
            'sqftToM2' => 'boolean',
            'size_sf' => [
                'required',
                'integer',
                'min:0',
                function ($attribute, $value, $fail) {
                    $buildingId = $this->route('building')->id ?? null;
                    $buildingAvailableId = $this->route('buildingAbsorption')->id ?? null;

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
