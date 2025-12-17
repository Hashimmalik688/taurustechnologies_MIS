<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'state' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'active_states' => ['nullable', 'array'],
            'active_states.*' => ['string', 'max:255'],
            'carriers' => ['nullable', 'array'],
            'carriers.*' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters long.',
            'state.required' => 'Please select a state.',
            'address.required' => 'The address field is required.',
            'active_states.array' => 'Invalid active states selection.',
            'carriers.array' => 'Invalid carriers data.',
        ];
    }
}
