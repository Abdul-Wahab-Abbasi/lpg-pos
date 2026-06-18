<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'refill_charge' => $this->refill_charge ?: 0,
            'return_deposit' => $this->return_deposit ?: 0,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'sale_price' => ['required', 'numeric', 'min:0'],
            'refill_charge' => ['nullable', 'numeric', 'min:0'],
            'return_deposit' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
