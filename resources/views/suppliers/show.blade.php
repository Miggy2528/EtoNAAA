@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Supplier Details Card -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Supplier Details</h5>
                    <div>
                        @can('update', $supplier)
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        @endcan
                        <a href="{{ route('purchases.create') }}" class="btn btn-sm btn-success"><i class="fas fa-shopping-cart"></i> Order</a>
                        <a href="{{ route('purchases.index') }}?supplier_id={{ $supplier->id }}" class="btn btn-sm btn-info"><i class="fas fa-list"></i> My Orders</a>
                        <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="text-center mb-3">
                        @if($supplier->photo)
                            <img src="{{ Storage::url($supplier->photo) }}" alt="{{ $supplier->name }}" class="img-fluid rounded mb-3" style="max-height: 150px;">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" alt="No Image" class="img-fluid rounded mb-3" style="max-height: 150px;">
                        @endif
                    </div>
                    
                    <h4>{{ $supplier->name }}</h4>
                    <p class="text-muted">{{ $supplier->shopname }}</p>
                    <p><strong>Type:</strong> {{ ucfirst($supplier->type_name) }}</p>
                    <p><strong>Status:</strong> 
                        <span class="badge bg-{{ $supplier->status === 'active' ? 'success' : 'danger' }}">
                            {{ ucfirst($supplier->status) }}
                        </span>
                    </p>

                    <hr>

                    <h6>Contact Information</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Email:</th>
                            <td>{{ $supplier->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $supplier->phone }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $supplier->address }}</td>
                        </tr>
                    </table>

                    <hr>

                    <h6>Bank Details</h6>
                    <table class="table table-sm">
                        <tr>
                            <th>Bank Name:</th>
                            <td>{{ $supplier->bank_name ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Account Holder:</th>
                            <td>{{ $supplier->account_holder ?? 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Account Number:</th>
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
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-truck"></i> Total Procurements</h6>
                            <h3>{{ $totalProc }}</h3>
                            <small>Deliveries</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-peso-sign"></i> Total Spent</h6>
                            <h3>₱{{ number_format($totalSpent, 0) }}</h3>
                            <small>All-time</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $onTimeRate >= 90 ? 'success' : ($onTimeRate >= 75 ? 'warning' : 'danger') }} text-white">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-clock"></i> On-Time Rate</h6>
                            <h3>{{ $onTimeRate }}%</h3>
                            <small>{{ $onTimeCount }}/{{ $totalProc }} deliveries</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-{{ $avgDefect <= 2 ? 'success' : ($avgDefect <= 3 ? 'warning' : 'danger') }} text-white">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-exclamation-triangle"></i> Defect Rate</h6>
                            <h3>{{ number_format($avgDefect, 2) }}%</h3>
                            <small>Average</small>
                        </div>
                    </div>
                </div>
            </div>

            
            <!-- Recent Procurements -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Procurements</h5>
                </div>
                <div class="card-body">
                    @if($recentProcurements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
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
                                    <td class="text-success"><strong>₱{{ number_format($proc->total_cost, 2) }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $proc->status === 'on-time' ? 'success' : 'warning' }}">
                                            {{ ucfirst($proc->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $proc->defective_rate <= 2 ? 'success' : ($proc->defective_rate <= 3 ? 'warning' : 'danger') }}">
                                            {{ number_format($proc->defective_rate, 2) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center py-4">No procurement records found.</p>
                    @endif
                </div>
            </div>

                    @if($products->count() > 0)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Supplied Products</h5>
                        </div>
                        <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                    <tr>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ $product->category->name }}</td>
                                        <td>{{ number_format($product->price, 2) }}</td>
                                        <td>{{ $product->stock }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        </div>
                    </div>
                    @endif

                    @can('update', $supplier)
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Assign Products</h5>
                        </div>
                        <div class="card-body">
                        <form action="{{ route('suppliers.assign-products', $supplier) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <select name="product_ids[]" class="form-select" multiple>
                                    @foreach(\App\Models\Product::all() as $product)
                                        <option value="{{ $product->id }}" {{ $products->contains($product->id) ? 'selected' : '' }}>
                                            {{ $product->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Products</button>
                        </form>
                        </div>
                    </div>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
