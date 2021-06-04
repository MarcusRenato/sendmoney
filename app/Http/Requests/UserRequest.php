<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name'     => 'required|string|min:3|max:150',
            'email'    => 'required|unique:users,email|email|string|min:3|max:50',
            'password' => 'required|confirmed|min:6|string',
            'type'     => 'required|string|in:comum,lojista',
            'cpf_cnpj' => 'required|string|unique:users,cpf_cnpj'
        ];
    }
}
