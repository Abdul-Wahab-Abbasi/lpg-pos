<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
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
            'category' => $this->category ?: 'Cylinder',
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
            'name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'refill_charge' => ['nullable', 'numeric', 'min:0'],
            'return_deposit' => ['nullable', 'numeric', 'min:0'],
            'unit' => ['required', Rule::in(['pcs', 'kg', 'ltr'])],
        ];
    }
}
