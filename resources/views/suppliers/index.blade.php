@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Suppliers</h3>
                    @can('create', App\Models\Supplier::class)
                    <div class="card-tools">
                        <a href="{{ route('suppliers.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add New Supplier
                        </a>
                        <a href="{{ route('purchases.create') }}" class="btn btn-success ms-2">
                            <i class="fas fa-shopping-cart"></i> Order Products
                        </a>
                    </div>
                    @endcan
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Shop Name</th>
                                    <th>Type</th>
                                    <th>Contact</th>
                                    <th>Procurements</th>
                                    <th>Total Spent</th>
                                    <th>On-Time Rate</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($suppliers as $supplier)
                                @php
                                    $procurement = $supplier->procurements->first();
                                    $totalDeliveries = $procurement->total_deliveries ?? 0;
                                    $onTimeDeliveries = $procurement->on_time_deliveries ?? 0;
                                    $onTimeRate = $totalDeliveries > 0 ? round(($onTimeDeliveries / $totalDeliveries) * 100, 1) : 0;
                                @endphp
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ $supplier->shopname }}</td>
                                    <td>{{ ucfirst($supplier->type->value ?? '') }}</td>

                                    <td>
                                        <strong>Email:</strong> {{ $supplier->email }}<br>
                                        <strong>Phone:</strong> {{ $supplier->phone }}
                                    </td>
                                    
                                    <td>
                                        <span class="badge badge-info">
                                            {{ $supplier->procurements_count }} deliveries
                                        </span>
                                    </td>
                                    
                                    <td>
                                        @if($procurement && $procurement->total_spent)
                                            <strong class="text-success">₱{{ number_format($procurement->total_spent, 2) }}</strong>
                                        @else
                                            <span class="text-muted">No data</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                        @if($totalDeliveries > 0)
                                            <div class="d-flex align-items-center">
                                                <div class="progress" style="width: 60px; height: 20px;">
                                                    <div class="progress-bar {{ $onTimeRate >= 90 ? 'bg-success' : ($onTimeRate >= 75 ? 'bg-warning' : 'bg-danger') }}" 
                                                         role="progressbar" 
                                                         style="width: {{ $onTimeRate }}%" 
                                                         aria-valuenow="{{ $onTimeRate }}" 
                                                         aria-valuemin="0" 
                                                         aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <small class="ms-2">{{ $onTimeRate }}%</small>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    
                                    <td>
                                    <span class="badge badge-{{ $supplier->status === 'active' ? 'success' : 'danger' }} text-dark">
                                    {{ ucfirst($supplier->status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="btn-group">
                                            @can('view', $supplier)
                                            <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('update', $supplier)
                                            <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            
                                            @can('delete', $supplier)
                                            <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this supplier?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">No suppliers found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $suppliers->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
