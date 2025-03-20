<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'unique:users,email,NULL,id,deleted_at,NULL',
            ],
            'user_name' => [
                'required',
                'string',
                'unique:users,user_name,NULL,id,deleted_at,NULL',
            ],
            'password' => 'required|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'total_devices' => 'nullable|integer|min:0',
            'status' => 'nullable|in:Enabled,Disabled',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}
