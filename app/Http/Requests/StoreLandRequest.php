<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandRequest extends FormRequest
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
            'sub_market_id' => 'required|integer|exists:cat_sub_markets,id',
            'industrial_park_id' => 'required|integer|exists:industrial_parks,id',
            'developer_id' => 'required|integer|exists:cat_developers,id',
            'owner_id' => 'required|integer|exists:cat_developers,id',
            'contact_id' => 'nullable|integer|exists:contacts,id',
            'land_name' => 'required|string|max:255',
            'currency' => 'required|in:USD,MXP',
            'latitud' => 'required|string|max:45',
            'longitud' => 'required|string|max:45',
            'size_ha' => 'integer',
            'kvas' => 'nullable|string|max:20',
            'zoning' => 'required|in:Industrial,Commercial,Residential',
            'parcel_shape' => 'required|in:Regular,Irregular',
            'status' => 'required|in:Enabled,Disabled,Pending,Approved',
        ];
    }
}
