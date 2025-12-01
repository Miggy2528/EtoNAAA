<?php

namespace App\Livewire;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class PurchaseForm extends Component
{
    public array $invoiceProducts = [];

    #[Validate('required', message: 'Please select at least one product')]
    public Collection $allProducts;

    public function mount(): void
    {
        $this->allProducts = Product::all();
    }

    public function render(): View
    {
        $total = 0;

        foreach ($this->invoiceProducts as $invoiceProduct) {
            if ($invoiceProduct['is_saved'] && $invoiceProduct['product_price'] && $invoiceProduct['quantity']) {
                $total += $invoiceProduct['product_price'] * $invoiceProduct['quantity'];
            }
        }

        return view('livewire.purchase-form', [
            'subtotal' => $total,
            'total' => $total,
        ]);
    }

    public function addProduct(): void
    {
        // Validate that all existing products are saved before adding a new one
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'Please save this product before adding another.');
                return;
            }
        }

        $this->invoiceProducts[] = [
            'product_id' => '',
            'quantity' => 1,
            'is_saved' => false,
            'product_name' => '',
            'product_price' => 0,
        ];
    }

    public function editProduct($index): void
    {
        // Validate that all existing products are saved before editing
        foreach ($this->invoiceProducts as $key => $invoiceProduct) {
            if (! $invoiceProduct['is_saved']) {
                $this->addError('invoiceProducts.'.$key, 'Please save this product before editing another.');
                return;
            }
        }

        $this->invoiceProducts[$index]['is_saved'] = false;
    }

    public function saveProduct($index): void
    {
        $this->resetErrorBag();

        // Validate product selection
        if (empty($this->invoiceProducts[$index]['product_id'])) {
            $this->addError('invoiceProducts.'.$index.'.product_id', 'Please select a product.');
            return;
        }

        // Validate quantity
        if (empty($this->invoiceProducts[$index]['quantity']) || $this->invoiceProducts[$index]['quantity'] <= 0) {
            $this->addError('invoiceProducts.'.$index.'.quantity', 'Quantity must be at least 1.');
            return;
        }

        $product = $this->allProducts->find($this->invoiceProducts[$index]['product_id']);

        if (!$product) {
            $this->addError('invoiceProducts.'.$index.'.product_id', 'Selected product not found.');
            return;
        }

        $this->invoiceProducts[$index]['product_name'] = $product->name;
        $this->invoiceProducts[$index]['product_price'] = $product->buying_price ?? 0;
        $this->invoiceProducts[$index]['is_saved'] = true;
    }

    public function removeProduct($index): void
    {
        unset($this->invoiceProducts[$index]);
        $this->invoiceProducts = array_values($this->invoiceProducts);
        $this->resetErrorBag();
    }
}