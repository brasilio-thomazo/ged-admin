<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'      => 'required|string|unique:clients,name',
            'identity'  => 'required|regex:/^[0-9]{11}([0-9]{3})?$/',
            'phone'     => 'required|regex:/^[0-9]{2}(9?)[0-9]{8}$/',
            'email'     => 'required|email',
            'scope'     => 'required|in:client,agente',
            'manager'   => 'required|string',
            'role'      => 'required|string',
        ];
    }
}
