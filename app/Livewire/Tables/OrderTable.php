<?php

namespace App\Livewire\Tables;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $statusFilter = '';

    public $paymentFilter = '';

    public $dateFrom = '';

    public $dateTo = '';

    public $sortField = 'invoice_no';

    public $sortAsc = false;

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function render()
    {
        $query = Order::query()
            ->with(['customer', 'details'])
            ->search($this->search);

        // Apply status filter
        if ($this->statusFilter) {
            $query->where('order_status', $this->statusFilter);
        }

        // Apply payment type filter
        if ($this->paymentFilter) {
            $query->where('payment_type', $this->paymentFilter);
        }

        // Apply date range filter
        if ($this->dateFrom) {
            $query->whereDate('order_date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('order_date', '<=', $this->dateTo);
        }

        return view('livewire.tables.order-table', [
            'orders' => $query
                ->orderByRaw("CASE order_status WHEN 'pending' THEN 1 WHEN 'for_delivery' THEN 2 WHEN 'complete' THEN 3 WHEN 'cancelled' THEN 4 ELSE 5 END")
                ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                ->paginate($this->perPage),
        ]);
    }
}
