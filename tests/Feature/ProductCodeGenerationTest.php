<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\MeatCut;
use App\Models\Category;
use App\Models\Unit;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductCodeGenerationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_generates_correct_product_code_when_creating_product()
    {
        // Create a user
        $user = User::factory()->create();
        
        // Create a category
        $category = Category::factory()->create(['name' => 'Meat']);
        
        // Create a unit
        $unit = Unit::factory()->create(['name' => 'Kilogram']);
        
        // Create a meat cut
        $meatCut = MeatCut::factory()->create([
            'name' => 'Chicken Wings',
            'animal_type' => 'chicken',
            'cut_type' => 'Wings'
        ]);
        
        // Login as user
        $this->actingAs($user);
        
        // Submit product creation form
        $response = $this->post(route('products.store'), [
            'name' => 'Fresh Chicken Wings',
            'category_id' => $category->id,
            'unit_id' => $unit->id,
            'meat_cut_id' => $meatCut->id,
            'quantity' => 10,
            'price_per_kg' => 200,
            'expiration_date' => '2025-12-31',
            'source' => 'Local Farm',
            'notes' => 'Fresh chicken wings',
            'buying_price' => 180,
            'selling_price' => 220,
            'quantity_alert' => 5,
        ]);
        
        // Assert that the product was created
        $response->assertSessionHas('success');
        
        // Check that the product has the correct code format
        $this->assertDatabaseHas('products', [
            'name' => 'Fresh Chicken Wings',
            'code' => 'CK-WNG-001'
        ]);
    }
}