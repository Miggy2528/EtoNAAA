<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Product;

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
            'product_image'     => 'nullable|image|file|max:2048',
            'name'              => 'sometimes|string',
            'slug'              => 'nullable|string',
            'category_id'       => 'sometimes|integer|exists:categories,id',
            'unit_id'           => 'sometimes|integer|exists:units,id',
            'meat_cut_id'       => 'sometimes|integer|exists:meat_cuts,id',
            'quantity'          => 'sometimes|integer|min:0',
            'price_per_kg'      => 'sometimes|numeric|min:0',
            'buying_price'      => 'sometimes|numeric|min:0',
            'quantity_alert'    => 'sometimes|integer|min:0',
            'expiration_date'   => 'nullable|date',
            'source'            => 'nullable|string',
            'notes'             => 'nullable|max:1000'
        ];
    }

    protected function prepareForValidation(): void
    {
        // Only generate slug if name is provided
        if ($this->filled('name')) {
            $baseSlug = Str::slug($this->name, '-');
            $slug = $baseSlug;
            $suffix = 2;
            $currentId = optional($this->product)->id;
            while (\App\Models\Product::where('slug', $slug)->when($currentId, function($q) use ($currentId) { $q->where('id', '!=', $currentId); })->exists()) {
                $slug = $baseSlug.'-'.$suffix;
                $suffix++;
            }
            $this->merge([
                'slug' => $slug,
            ]);
        }
    }
}