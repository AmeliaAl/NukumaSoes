<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorecoaRequest extends FormRequest
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
            'kode_akun' => ['required', 'string', 'max:50'],
            'header_akun' => ['nullable', 'string', 'max:100'],
            'nama_akun' => ['required', 'string', 'max:255'],
        ];
    }
}
