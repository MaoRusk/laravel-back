<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandAbsorptionRequest extends FormRequest
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
            'land_condition' => 'nullable|in:Fully Developed,Undeveloped',
            'rail_spur' => 'nullable|boolean',
            'natural_gas' => 'nullable|in:yes,no,feasibility',
            'sewage' => 'nullable|in:yes,no,feasibility',
            'water' => 'nullable|in:yes,no,feasibility',
            'electric' => 'nullable|in:yes,no,feasibility',
            'kvas' => 'nullable|integer',
            'abs_company_id' => 'nullable|exists:companies,id',
            'abs_country_id' => 'nullable|exists:countries,id',
            'abs_size_ha' => 'nullable|integer',
            'abs_closing_price' => 'nullable|numeric|min:0',
            'abs_type_buyer' => 'nullable|in:User,Developer',
            'abs_company_type' => 'nullable|in:Existing Company,New Company in Market,New Company in Mexico',
            'abs_industry_id' => 'nullable|exists:cat_industries,id',
            'abs_final_use' => 'nullable|in:Logistics,Manufacturing,TBD',
            'abs_broker_id' => 'nullable|exists:cat_brokers,id',
            'abs_comments' => 'nullable|string|max:255',
            'abs_kvas_price' => 'nullable|numeric|min:0',
        ];
    }
}
