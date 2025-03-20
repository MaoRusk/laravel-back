<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'middle_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($this->user->id)->whereNull('deleted_at'),
            ],
            'user_name' => [
                'sometimes',
                'string',
                Rule::unique('users', 'user_name')->ignore($this->user->id)->whereNull('deleted_at'),
            ],
            'password' => 'sometimes|string|min:8',
            'role_id' => 'nullable|exists:roles,id',
            'total_devices' => 'nullable|integer|min:0',
            'status' => 'nullable|in:Enabled,Disabled',
            'updated_by' => 'nullable|exists:users,id',
        ];
    }
}
