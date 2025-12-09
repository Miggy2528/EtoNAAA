<?php

namespace App\Livewire\Tables;

use App\Models\Purchase;
use App\Models\Supplier;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class PurchaseTable extends Component
{
    use WithPagination;

    public $perPage = 10;

    public $search = '';

    public $sortField = 'purchase_no';

    public $sortAsc = false;
    
    public $supplierId = null;
    
    public $statusFilter = '';
    
    public $dateFrom = '';
    
    public $dateTo = '';

    public function mount()
    {
        $this->supplierId = request()->get('supplier_id');
        $this->statusFilter = request()->get('status', '');
        $this->dateFrom = request()->get('date_from', '');
        $this->dateTo = request()->get('date_to', '');
    }

    public function sortBy($field): void
    {
        if ($this->sortField === $field) {
            $this->sortAsc = ! $this->sortAsc;

        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }
    
    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $query = Purchase::query()->with('supplier');
        
        // Apply supplier filter
        if ($this->supplierId) {
            $query->where('supplier_id', $this->supplierId);
        }
        
        // Apply status filter
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }
        
        // Apply date range filter
        if ($this->dateFrom) {
            $query->whereDate('date', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('date', '<=', $this->dateTo);
        }
        
        // Apply search
        $query->search($this->search);
        
        $purchases = $query->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
            ->paginate($this->perPage);
        
        // Get supplier info if filtering by supplier
        $supplier = null;
        $stats = null;
        if ($this->supplierId) {
            $supplier = Supplier::find($this->supplierId);
            $stats = $this->getSupplierStats($this->supplierId);
        }
        
        return view('livewire.tables.purchase-table', [
            'purchases' => $purchases,
            'supplier' => $supplier,
            'stats' => $stats,
        ]);
    }
    
    private function getSupplierStats($supplierId)
    {
        return [
            'total_orders' => Purchase::where('supplier_id', $supplierId)->count(),
            'total_amount' => Purchase::where('supplier_id', $supplierId)->sum('total_amount'),
            'pending_orders' => Purchase::where('supplier_id', $supplierId)->where('status', 0)->count(),
            'completed_orders' => Purchase::where('supplier_id', $supplierId)->whereIn('status', [3, 4])->count(),
        ];
    }
}
