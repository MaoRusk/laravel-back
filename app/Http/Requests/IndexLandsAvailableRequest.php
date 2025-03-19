<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexLandsAvailableRequest extends FormRequest
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
            'page' => 'nullable|integer',
            'size' => 'nullable|integer',
            'search' => 'nullable|string',

            'state' => 'nullable|string',
            'land_condition' => 'nullable|in:Fully Developed,Undeveloped',
            'avl_size_ha' => 'nullable|integer',
            'avl_broker_id' => 'nullable|integer',
            'avl_deal' => 'nullable|in:Lease,Sale',
            'avl_minimum_size_ha' => 'nullable|integer',

            'column' => 'nullable|in:state,land_condition,avl_size_ha,avl_broker_id,avl_deal,avl_minimum_size_ha',
        ];
    }
}
