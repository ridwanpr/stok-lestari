<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        $expiryRules = ['required', 'date'];

        if ($this->isMethod('POST')) {
            $expiryRules[] = 'after_or_equal:today';
        }

        return [
            'stock' => ['required', 'integer', 'min:0'],
            'expiry_date' => $expiryRules,
            'status' => ['required', 'in:active,expired,sold_out'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'stock' => 'stok',
            'expiry_date' => 'tanggal kedaluwarsa',
            'status' => 'status',
        ];
    }
}
