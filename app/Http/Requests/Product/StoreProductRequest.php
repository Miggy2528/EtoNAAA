<?php

namespace App\Http\Requests\Product;

use Illuminate\Support\Str;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\MeatCut;
use App\Models\Product;

class StoreProductRequest extends FormRequest
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
            'product_image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'name'              => 'required|string',
            // allow duplicate names by removing unique slug constraint
            'slug'              => 'required|string',
            'code'              => 'nullable|string|unique:products',
            'category_id'       => 'required|integer',
            'unit_id'           => 'required|integer',
            'meat_cut_id'       => 'required|integer|exists:meat_cuts,id',
            'quantity'          => 'required|integer|min:0',
            'price_per_kg'      => 'required|numeric|min:0',
            'expiration_date'   => 'required|date|after:today',
            'source'            => 'required|string',
            'notes'             => 'nullable|string|max:1000',
            'buying_price'      => 'required|numeric|min:0',
            'quantity_alert'    => 'required|integer|min:0',
        ];
    }

    protected function prepareForValidation(): void
    {
        $code = $this->code;
        if (!$code) {
            // Generate unique code in format: ANIMAL-CUT-XXX
            $meatCut = MeatCut::find($this->meat_cut_id);
            if ($meatCut) {
                $code = $this->generateProductCode($meatCut);
            } else {
                // Fallback if meat cut not found
                do {
                    $code = 'PC' . strtoupper(uniqid());
                } while (Product::where('code', $code)->exists());
            }
        }
        
        // Generate a readable unique slug with auto-suffix
        $baseSlug = Str::slug($this->name, '-');
        $slug = $baseSlug;
        $suffix = 2;
        while (Product::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }
        $this->merge([
            'slug' => $slug,
            'code' => $code,
        ]);
    }

    /**
     * Generate a product code in the format: ANIMAL-CUT-XXX
     * Example: CK-WNG-001 (Chicken Wings 001)
     */
    private function generateProductCode(MeatCut $meatCut): string
    {
        // Animal type abbreviations
        $animalAbbreviations = [
            'beef' => 'BF',
            'pork' => 'PK',
            'chicken' => 'CK',
            'lamb' => 'LB',
            'goat' => 'GT'
        ];
        
        // Get animal abbreviation or use first 2 letters capitalized
        $animalType = strtolower($meatCut->animal_type);
        $animalCode = $animalAbbreviations[$animalType] ?? strtoupper(substr($animalType, 0, 2));
        
        // Generate cut abbreviation from cut name
        $cutCode = $this->generateCutAbbreviation($meatCut->name);
        
        // Find the next sequential number for this animal-cut combination
        $sequence = $this->getNextSequenceNumber($animalCode, $cutCode);
        
        return sprintf('%s-%s-%03d', $animalCode, $cutCode, $sequence);
    }
    
    /**
     * Generate a cut abbreviation from the cut name
     * Examples: "Chicken Wings" -> "WNG", "Ribeye" -> "RIB"
     */
    private function generateCutAbbreviation(string $cutName): string
    {
        // Common cut abbreviations
        $cutAbbreviations = [
            'breast' => 'BRS',
            'thigh' => 'THI',
            'wings' => 'WNG',
            'ribeye' => 'RIB',
            'sirloin' => 'SIR',
            'tenderloin' => 'TEN',
            't-bone' => 'TBN',
            'brisket' => 'BRS',
            'chop' => 'CHP',
            'belly' => 'BEL',
            'ribs' => 'RIB',
            'shank' => 'SHK'
        ];
        
        // Convert to lowercase for matching
        $lowerCutName = strtolower($cutName);
        
        // Check if we have a predefined abbreviation
        foreach ($cutAbbreviations as $cut => $abbrev) {
            if (strpos($lowerCutName, $cut) !== false) {
                return $abbrev;
            }
        }
        
        // Fallback: take first 3 letters of the last word
        $words = explode(' ', $cutName);
        $lastWord = strtolower(end($words));
        return strtoupper(substr($lastWord, 0, 3));
    }
    
    /**
     * Get the next sequence number for a given animal-cut combination
     */
    private function getNextSequenceNumber(string $animalCode, string $cutCode): int
    {
        // Find existing products with the same animal-cut prefix
        $existingCode = Product::where('code', 'like', "$animalCode-$cutCode-%")
            ->orderBy('code', 'desc')
            ->first();
            
        if ($existingCode) {
            // Extract the sequence number from the last product code
            $parts = explode('-', $existingCode->code);
            if (count($parts) == 3 && is_numeric($parts[2])) {
                return (int)$parts[2] + 1;
            }
        }
        
        // Start with 1 if no existing products found
        return 1;
    }

    public function messages(): array
    {
        return [
            'expiration_date.after' => 'The expiration date must be a future date.',
            'price_per_kg.min' => 'The price per kilogram must be greater than 0.',
        ];
    }
}