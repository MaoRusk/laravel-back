<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandAvailableRequest extends FormRequest
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
            'avl_broker_id' => 'nullable|exists:cat_brokers,id',
            'avl_size_ha' => 'nullable|integer',
            'avl_minimum_size_ha' => 'nullable|integer',
            'avl_min_sale' => 'nullable|numeric|min:0',
            'avl_max_sale' => 'nullable|numeric|min:0',
            'avl_conditioned_construction' => 'nullable|boolean',
            'avl_deal' => 'required|in:Lease,Sale',
            'avl_comments' => 'nullable|string|max:255',
        ];
    }
}
