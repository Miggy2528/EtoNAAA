<?php

namespace Database\Seeders;

use App\Models\MeatCut;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MeatClassificationsSeeder extends Seeder
{
    /**
     * Run the database seeds to add classifications to existing meat cuts.
     */
    public function run(): void
    {
        // Update existing meat cuts with classification data
        $meatCuts = [
            // Beef cuts
            'Ribeye' => [
                'meat_type' => 'beef',
                'meat_subtype' => 'steak',
                'quality' => 'premium',
                'quality_grade' => 'prime',
                'preparation_type' => 'grill',
                'preparation_style' => 'dry_aged'
            ],
            'Sirloin' => [
                'meat_type' => 'beef',
                'meat_subtype' => 'steak',
                'quality' => 'choice',
                'quality_grade' => 'choice',
                'preparation_type' => 'grill',
                'preparation_style' => 'marinated'
            ],
            'Tenderloin' => [
                'meat_type' => 'beef',
                'meat_subtype' => 'tender',
                'quality' => 'premium',
                'quality_grade' => 'prime',
                'preparation_type' => 'roast',
                'preparation_style' => 'herb_crusted'
            ],
            'T-Bone' => [
                'meat_type' => 'beef',
                'meat_subtype' => 'steak',
                'quality' => 'choice',
                'quality_grade' => 'choice',
                'preparation_type' => 'grill',
                'preparation_style' => 'classic'
            ],
            'Brisket' => [
                'meat_type' => 'beef',
                'meat_subtype' => 'brisket',
                'quality' => 'standard',
                'quality_grade' => 'select',
                'preparation_type' => 'smoke',
                'preparation_style' => 'slow_smoked'
            ],
            
            // Pork cuts
            'Pork Chop' => [
                'meat_type' => 'pork',
                'meat_subtype' => 'chop',
                'quality' => 'choice',
                'quality_grade' => 'choice',
                'preparation_type' => 'grill',
                'preparation_style' => 'pan_seared'
            ],
            'Pork Belly' => [
                'meat_type' => 'pork',
                'meat_subtype' => 'belly',
                'quality' => 'standard',
                'quality_grade' => 'select',
                'preparation_type' => 'roast',
                'preparation_style' => 'crispy'
            ],
            'Pork Ribs' => [
                'meat_type' => 'pork',
                'meat_subtype' => 'ribs',
                'quality' => 'choice',
                'quality_grade' => 'choice',
                'preparation_type' => 'bbq',
                'preparation_style' => 'glazed'
            ],
            
            // Chicken cuts
            'Chicken Breast' => [
                'meat_type' => 'chicken',
                'meat_subtype' => 'breast',
                'quality' => 'standard',
                'quality_grade' => 'a_grade',
                'preparation_type' => 'grill',
                'preparation_style' => 'lemon_pepper'
            ],
            'Chicken Thigh' => [
                'meat_type' => 'chicken',
                'meat_subtype' => 'thigh',
                'quality' => 'standard',
                'quality_grade' => 'a_grade',
                'preparation_type' => 'roast',
                'preparation_style' => 'garlic_herb'
            ],
            'Chicken Wings' => [
                'meat_type' => 'chicken',
                'meat_subtype' => 'wing',
                'quality' => 'standard',
                'quality_grade' => 'b_grade',
                'preparation_type' => 'fry',
                'preparation_style' => 'buffalo'
            ],
            
            // Lamb cuts
            'Lamb Chops' => [
                'meat_type' => 'lamb',
                'meat_subtype' => 'chop',
                'quality' => 'premium',
                'quality_grade' => 'choice',
                'preparation_type' => 'grill',
                'preparation_style' => 'mediterranean'
            ],
            'Lamb Shank' => [
                'meat_type' => 'lamb',
                'meat_subtype' => 'shank',
                'quality' => 'choice',
                'quality_grade' => 'choice',
                'preparation_type' => 'braise',
                'preparation_style' => 'red_wine'
            ],
        ];

        // Update each meat cut with its classification data
        foreach ($meatCuts as $cutName => $classification) {
            MeatCut::where('name', $cutName)->update($classification);
        }

        $this->command->info('Updated meat cuts with classification data!');
    }
}