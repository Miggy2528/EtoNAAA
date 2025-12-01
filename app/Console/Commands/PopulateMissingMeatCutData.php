<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MeatCut;

class PopulateMissingMeatCutData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-missing-meat-cut-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate missing meat type and quality grade data for meat cuts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for meat cuts with missing data...');
        
        // Get meat cuts with missing meat_type or quality_grade
        $missingDataCuts = MeatCut::whereNull('meat_type')
            ->orWhereNull('quality_grade')
            ->get();
            
        if ($missingDataCuts->isEmpty()) {
            $this->info('No meat cuts with missing data found.');
            return;
        }
        
        $this->info('Found ' . $missingDataCuts->count() . ' meat cuts with missing data.');
        
        // Define default classifications
        $defaultClassifications = [
            'beef' => [
                'quality_grade' => 'choice',
                'meat_subtype' => 'cut',
                'quality' => 'standard',
                'preparation_type' => 'grill',
                'preparation_style' => 'classic'
            ],
            'pork' => [
                'quality_grade' => 'choice',
                'meat_subtype' => 'cut',
                'quality' => 'standard',
                'preparation_type' => 'roast',
                'preparation_style' => 'classic'
            ],
            'chicken' => [
                'quality_grade' => 'a_grade',
                'meat_subtype' => 'cut',
                'quality' => 'standard',
                'preparation_type' => 'grill',
                'preparation_style' => 'classic'
            ],
            'lamb' => [
                'quality_grade' => 'choice',
                'meat_subtype' => 'cut',
                'quality' => 'standard',
                'preparation_type' => 'roast',
                'preparation_style' => 'classic'
            ],
            'goat' => [
                'quality_grade' => 'choice',
                'meat_subtype' => 'cut',
                'quality' => 'standard',
                'preparation_type' => 'stew',
                'preparation_style' => 'spicy'
            ]
        ];
        
        foreach ($missingDataCuts as $cut) {
            $this->info("Processing: {$cut->name}");
            
            // Try to determine meat type from name if not set
            $meatType = $cut->meat_type;
            if (!$meatType) {
                $nameLower = strtolower($cut->name);
                foreach (array_keys($defaultClassifications) as $type) {
                    if (strpos($nameLower, $type) !== false) {
                        $meatType = $type;
                        break;
                    }
                }
                
                // If still not found, default to beef
                if (!$meatType) {
                    $meatType = 'beef';
                }
            }
            
            // Set the data
            $updateData = [
                'meat_type' => $meatType,
                'quality_grade' => $cut->quality_grade ?? $defaultClassifications[$meatType]['quality_grade']
            ];
            
            // Only update fields that are null
            if (!$cut->meat_subtype) {
                $updateData['meat_subtype'] = $defaultClassifications[$meatType]['meat_subtype'];
            }
            
            if (!$cut->quality) {
                $updateData['quality'] = $defaultClassifications[$meatType]['quality'];
            }
            
            if (!$cut->preparation_type) {
                $updateData['preparation_type'] = $defaultClassifications[$meatType]['preparation_type'];
            }
            
            if (!$cut->preparation_style) {
                $updateData['preparation_style'] = $defaultClassifications[$meatType]['preparation_style'];
            }
            
            $cut->update($updateData);
            $this->info("Updated: {$cut->name} with meat type: {$meatType}");
        }
        
        $this->info('Finished populating missing meat cut data!');
    }
}
