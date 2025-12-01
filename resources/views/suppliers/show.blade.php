@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title mb-1">Supplier Profile</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $supplier->name }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @can('update', $supplier)
                    <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                        <i class="fas fa-edit me-2 text-white"></i><span class="text-white">Edit Supplier</span>
                    </a>
                    @endcan
                    <a href="{{ route('purchases.create') }}" class="btn btn-success ms-2" style="background-color: #198754; border-color: #198754;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                        <i class="fas fa-shopping-cart me-2 text-white"></i><span class="text-white">Order Products</span>
                    </a>
                    <a href="{{ route('suppliers.purchase-order', $supplier) }}" class="btn btn-primary ms-2" style="background-color: #0d6efd; border-color: #0d6efd;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                        <i class="fas fa-file-invoice me-2 text-white"></i><span class="text-white">Generate Purchase Order</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-secondary ms-2" style="background-color: #6c757d; border-color: #6c757d;" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                        <i class="fas fa-arrow-left me-2 text-white"></i><span class="text-white">Back to Suppliers</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Supplier Details Card -->
        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">Supplier Information</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        @if($supplier->photo)
                            <img src="{{ Storage::url($supplier->photo) }}" alt="{{ $supplier->name }}" class="img-fluid rounded-circle mb-3" style="max-height: 150px; width: 150px; object-fit: cover;">
                        @else
                            <div class="bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 150px; height: 150px;">
                                <i class="fas fa-user text-muted fa-3x"></i>
                            </div>
                        @endif
                        <h4 class="mb-1">{{ $supplier->name }}</h4>
                        <p class="text-muted mb-2">{{ $supplier->shopname }}</p>
                        <span class="badge {{ $supplier->status === 'active' ? 'bg-success' : 'bg-danger' }} fs-6">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </div>
                    
                    <hr>
                    
                    <h6 class="mb-3"><i class="fas fa-address-card me-2 text-primary"></i>Contact Information</h6>
                    <table class="table table-borderless table-sm mb-4">
                        <tr>
                            <td class="text-muted"><i class="fas fa-envelope me-2"></i>Email</td>
                            <td>{{ $supplier->email }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-phone me-2"></i>Phone</td>
                            <td>{{ $supplier->phone }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>Address</td>
                            <td>{{ $supplier->address }}</td>
                        </tr>
                    </table>

                    <h6 class="mb-3"><i class="fas fa-university me-2 text-primary"></i>Bank Details</h6>
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td class="text-muted">Bank Name</td>
                            <td>{{ $supplier->bank_name ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Account Holder</td>
                            <td>{{ $supplier->account_holder ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Account Number</td>
                            <td>{{ $supplier->account_number ?? 'Not provided' }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Performance Analytics Card -->
        <div class="col-md-8">
            @php
                $totalProc = $procurementStats->total_procurements ?? 0;
                $totalSpent = $procurementStats->total_spent ?? 0;
                $onTimeCount = $procurementStats->on_time_count ?? 0;
                $onTimeRate = $totalProc > 0 ? round(($onTimeCount / $totalProc) * 100, 1) : 0;
                $avgDefect = $procurementStats->avg_defect_rate ?? 0;
            @endphp
            
            <!-- Performance Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-start border-4 border-primary h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-primary bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-truck text-primary"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">{{ $totalProc }}</h5>
                                    <p class="mb-0 text-muted small">Procurements</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 border-success h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 bg-success bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-peso-sign text-success"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">₱{{ number_format($totalSpent, 0) }}</h5>
                                    <p class="mb-0 text-muted small">Total Spent</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 {{ $onTimeRate >= 90 ? 'border-success' : ($onTimeRate >= 75 ? 'border-warning' : 'border-danger') }} h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 {{ $onTimeRate >= 90 ? 'bg-success' : ($onTimeRate >= 75 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-clock {{ $onTimeRate >= 90 ? 'text-success' : ($onTimeRate >= 75 ? 'text-warning' : 'text-danger') }}"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">{{ $onTimeRate }}%</h5>
                                    <p class="mb-0 text-muted small">On-Time Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-start border-4 {{ $avgDefect <= 2 ? 'border-success' : ($avgDefect <= 3 ? 'border-warning' : 'border-danger') }} h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 {{ $avgDefect <= 2 ? 'bg-success' : ($avgDefect <= 3 ? 'bg-warning' : 'bg-danger') }} bg-opacity-10 p-2 rounded-circle">
                                    <i class="fas fa-exclamation-triangle {{ $avgDefect <= 2 ? 'text-success' : ($avgDefect <= 3 ? 'text-warning' : 'text-danger') }}"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-0">{{ number_format($avgDefect, 2) }}%</h5>
                                    <p class="mb-0 text-muted small">Defect Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Procurements -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-history me-2 text-primary"></i>Recent Procurements</h5>
                        <a href="{{ route('purchases.index') }}?supplier_id={{ $supplier->id }}" class="btn btn-sm btn-primary d-flex align-items-center">
                            <i class="fas fa-list me-1"></i>
                            View All Orders
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentProcurements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th>Defect Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentProcurements as $proc)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($proc->delivery_date)->format('M d, Y') }}</td>
                                    <td>
                                        <strong>{{ $proc->product->name ?? 'N/A' }}</strong>
                                    </td>
                                    <td>{{ number_format($proc->quantity_supplied) }} {{ $proc->product->unit->name ?? 'units' }}</td>
                                    <td class="text-success fw-bold">₱{{ number_format($proc->total_cost, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $proc->status === 'on-time' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($proc->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $proc->defective_rate <= 2 ? 'bg-success' : ($proc->defective_rate <= 3 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($proc->defective_rate, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-file-invoice fa-2x text-muted mb-3"></i>
                        <h5>No procurement records found</h5>
                        <p class="text-muted">This supplier doesn't have any procurement history yet.</p>
                        <a href="{{ route('purchases.create') }}" class="btn btn-primary">Create First Order</a>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Supplied Products -->
            @if($products->count() > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-boxes me-2 text-primary"></i>Supplied Products</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Category</th>
                                    <th>Unit Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                <tr>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="badge bg-info">{{ $product->category->name }}</span></td>
                                    <td>Buying: ₱{{ number_format($product->buying_price ?? 0, 2) }}</td>
                                    <td>
                                        @php
                                            $latestPurchase = $product->purchases->first();
                                        @endphp
                                        @if($latestPurchase)
                                            <span class="badge 
                                                @if($latestPurchase->status->value === 0) bg-warning
                                                @elseif($latestPurchase->status->value === 1) bg-primary
                                                @elseif($latestPurchase->status->value === 2) bg-info
                                                @elseif($latestPurchase->status->value === 3) bg-success
                                                @elseif($latestPurchase->status->value === 4) bg-secondary
                                                @endif">
                                                {{ $latestPurchase->status->label() }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No Orders</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
            
            <!-- Assign Products Section -->
            @can('update', $supplier)
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-link me-2 text-primary"></i>Assign Products</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.assign-products', $supplier) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Select Products</label>
                            <select name="product_ids[]" class="form-select" multiple size="8">
                                @foreach(\App\Models\Product::all() as $product)
                                    <option value="{{ $product->id }}" {{ $products->contains($product->id) ? 'selected' : '' }}>
                                        {{ $product->name }} (Buying: ₱{{ number_format($product->buying_price ?? 0, 2) }} | Selling: ₱{{ number_format($product->selling_price ?? 0, 2) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Hold Ctrl (Cmd on Mac) to select multiple products</div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Product Assignments</button>
                    </form>
                </div>
            </div>
            @endcan
        </div>
    </div>
</div>
@endsection
