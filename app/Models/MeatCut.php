<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class MeatCut extends Model
{
    use HasFactory, Sortable;

    protected $fillable = [
        'name',
        'description',
        'meat_type',
        'cut_type',
        'default_price_per_kg',
        'quantity',
        'is_available',
        'is_by_product',
        'is_processing_meat',
        'minimum_stock_level',
        'image_path',
        'meat_subtype',
        'quality',
        'quality_grade',
        'preparation_type',
        'preparation_style'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'quantity' => 'integer',
        'minimum_stock_level' => 'integer',
        'default_price_per_kg' => 'decimal:2'
    ];

    public $sortable = [
        'name',
        'meat_type',
        'quality_grade',
        'default_price_per_kg',
        'quantity',
        'is_available',
        'created_at'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function inventory()
    {
        return $this->hasMany(Product::class, 'meat_cut_id');
    }

    public function orders()
    {
        return $this->hasMany(OrderDetail::class, 'meat_cut_id');
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->minimum_stock_level;
    }

    public function updateQuantity(int $change): void
    {
        $this->quantity += $change;
        $this->is_available = $this->quantity > 0;
        $this->save();
    }
} 