<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'product_image'     => 'image|file|max:2048',
            'name'              => 'required|string',
            'slug'              => [
                Rule::unique('products')->ignore($this->product)
            ],
            'category_id'       => 'required|integer',
            'unit_id'           => 'required|integer',
            'meat_cut_id'       => 'required|integer|exists:meat_cuts,id',
            'quantity'          => 'required|integer',
            'price_per_kg'      => 'required|numeric|min:0',
            'buying_price'      => 'required|numeric|min:0',
            'selling_price'     => 'required|numeric|min:0',
            'quantity_alert'    => 'required|integer',
            'expiration_date'   => 'required|date|after:today',
            'source'            => 'required|string',
            'notes'             => 'nullable|max:1000'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => Str::slug($this->name, '-'),
        ]);
    }
}
