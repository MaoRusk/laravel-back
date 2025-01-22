<?php

namespace App\Http\Requests;

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
            'abs_tenant_id' => 'required|integer|exists:cat_tenants,id',
            'abs_industry_id' => 'required|integer|exists:cat_industries,id',
            'abs_country_id' => 'required|integer|exists:countries,id',
            'broker_id' => 'required|integer|exists:cat_developers,id',
            'abs_lease_term_month' => 'nullable|integer|min:0',
            'abs_asking_rate_shell' => 'required|numeric|min:0',
            'abs_closing_rate' => 'required|numeric|min:0',
            'abs_closing_date' => 'nullable|date',
            'abs_company_type' => 'nullable|in:Existing Company,New Company in Market,New Company in Mexico',
            'abs_lease_up' => 'nullable|date',
            'abs_month' => 'nullable|date',
            'abs_sale_price' => 'nullable|numeric|min:0',
            'abs_final_use' => 'nullable|in:Logistic,Manufacturing',
            'abs_building_phase' => 'required|in:BTS,Expansion,Inventory',
            'deal' => 'required|in:Sale,Lease',
            'currency' => 'required|in:USD,MXP',
            'has_expansion_land' => 'required|boolean',
            'has_crane' => 'required|boolean',
            'has_hvac' => 'required|boolean',
            'has_rail_spur' => 'required|boolean',
            'has_sprinklers' => 'required|boolean',
            'has_office' => 'required|boolean',
            'has_leed' => 'required|boolean',
        ];
    }
}
