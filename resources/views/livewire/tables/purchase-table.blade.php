<div>
    <style>
        .purchase-table-wrapper .card {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
            border: none;
        }
        .purchase-table-wrapper .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        .purchase-table-wrapper .card-header h3 {
            color: white;
            margin: 0;
        }
        .purchase-table-wrapper .supplier-header-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }
        .purchase-table-wrapper .supplier-header-card a {
            color: white;
        }
        .purchase-table-wrapper .stats-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .purchase-table-wrapper .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1);
        }
        .purchase-table-wrapper .avatar {
            width: 3rem;
            height: 3rem;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .purchase-table-wrapper .table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }
        .purchase-table-wrapper .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
            font-size: 0.75rem;
        }
        .purchase-table-wrapper .btn-icon {
            margin: 0 0.15rem;
        }
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
        }
    </style>
    
    <div class="purchase-table-wrapper">
    @if($supplier)
    <!-- Supplier Information Header -->
    <div class="card mb-4 supplier-header-card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-xl rounded" style="background-color: rgba(255,255,255,0.2);">
                        <span class="text-white fs-1 fw-bold">{{ strtoupper(substr($supplier->name, 0, 2)) }}</span>
                    </div>
                </div>
                <div class="col">
                    <h2 class="mb-1 text-white">
                        <i class="fas fa-building me-2"></i>{{ $supplier->name }}
                    </h2>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-white opacity-75">
                                <i class="fas fa-envelope me-1"></i>
                                <a href="mailto:{{ $supplier->email }}" class="text-white text-decoration-none">{{ $supplier->email }}</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white opacity-75">
                                <i class="fas fa-phone me-1"></i>
                                <a href="tel:{{ $supplier->phone }}" class="text-white text-decoration-none">{{ $supplier->phone }}</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-white opacity-75">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $supplier->address ?? 'N/A' }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <a href="{{ route('suppliers.show', $supplier) }}" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>Back to Supplier
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @if($stats)
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm stats-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-primary text-white avatar">
                                <i class="fas fa-shopping-cart"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium fs-3">
                                {{ $stats['total_orders'] ?? 0 }}
                            </div>
                            <div class="text-muted small">
                                Total Orders
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm stats-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-success text-white avatar">
                                <i class="fas fa-peso-sign"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium fs-4">
                                ₱{{ number_format($stats['total_amount'] ?? 0, 2) }}
                            </div>
                            <div class="text-muted small">
                                Total Amount
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm stats-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-warning text-white avatar">
                                <i class="fas fa-clock"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium fs-3">
                                {{ $stats['pending_orders'] ?? 0 }}
                            </div>
                            <div class="text-muted small">
                                Pending Orders
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card card-sm stats-card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <span class="bg-info text-white avatar">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                        <div class="col">
                            <div class="font-weight-medium fs-3">
                                {{ $stats['completed_orders'] ?? 0 }}
                            </div>
                            <div class="text-muted small">
                                Completed Orders
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    @endif

    <!-- Main Purchase Table Card -->
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">
                    @if($supplier)
                        <i class="fas fa-list-alt me-2"></i>Purchase Orders from {{ $supplier->name }}
                    @else
                        {{ __('Purchases') }}
                    @endif
                </h3>
            </div>

            <div class="card-actions">
                <x-action.create route="{{ route('purchases.create') }}" />
            </div>
        </div>

        <!-- Advanced Filters -->
        <div class="card-body border-bottom py-3">
            <div class="row g-3">
                <!-- Search -->
                <div class="col-md-3">
                    <label class="form-label small mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Search purchases...">
                    </div>
                </div>
                
                <!-- Status Filter -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="0">Pending</option>
                        <option value="1">Approved</option>
                        <option value="2">For Delivery</option>
                        <option value="3">Complete</option>
                        <option value="4">Received</option>
                    </select>
                </div>
                
                <!-- Date From -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Date From</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                
                <!-- Date To -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Date To</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                
                <!-- Per Page -->
                <div class="col-md-2">
                    <label class="form-label small mb-1">Show Entries</label>
                    <select wire:model.live="perPage" class="form-select">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                
                <!-- Clear Filters -->
                <div class="col-md-1">
                    <label class="form-label small mb-1">&nbsp;</label>
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100" title="Clear all filters">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </div>
        </div>

        <x-spinner.loading-spinner/>

    <div class="table-responsive">
        <table wire:loading.remove class="table table-bordered card-table table-vcenter text-nowrap datatable">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('purchase_no')" href="#" role="button">
                            {{ __('Purchase No.') }}
                            @include('inclues._sort-icon', ['field' => 'purchase_no'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('supplier_id')" href="#" role="button">
                            {{ __('Supplier') }}
                            @include('inclues._sort-icon', ['field' => 'supplier_id'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('date')" href="#" role="button">
                            {{ __('Date') }}
                            @include('inclues._sort-icon', ['field' => 'date'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('total_amount')" href="#" role="button">
                            {{ __('Total') }}
                            @include('inclues._sort-icon', ['field' => 'total_amount'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('status')" href="#" role="button">
                            {{ __('Status') }}
                            @include('inclues._sort-icon', ['field' => 'status'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        {{ __('Action') }}
                    </th>
                </tr>
            </thead>
            <tbody>
            @forelse ($purchases as $purchase)
                <tr>
                    <td class="align-middle text-center">
                        {{ $loop->iteration }}
                    </td>
                    <td class="align-middle text-center">
                        {{ $purchase->purchase_no }}
                    </td>
                    <td class="align-middle">
                        {{ $purchase->supplier?->name ?? 'N/A' }}
                    </td>
                    <td class="align-middle text-center">
                        {{ $purchase->date->format('d-m-Y') }}
                    </td>
                    <td class="align-middle text-center">
₱{{ number_format($purchase->total_amount, 2) }}
                    </td>

                    <td class="align-middle text-center">
                        @php
                            $statusColors = [
                                \App\Enums\PurchaseStatus::PENDING->value => 'bg-warning',
                                \App\Enums\PurchaseStatus::APPROVED->value => 'bg-success',
                                \App\Enums\PurchaseStatus::FOR_DELIVERY->value => 'bg-info',
                                \App\Enums\PurchaseStatus::COMPLETE->value => 'bg-primary',
                                \App\Enums\PurchaseStatus::RECEIVED->value => 'bg-success',
                            ];
                            $statusValue = is_object($purchase->status) ? $purchase->status->value : $purchase->status;
                            $colorClass = $statusColors[$statusValue] ?? 'bg-secondary';
                            $statusLabel = is_object($purchase->status) ? $purchase->status->label() : 'Unknown';
                        @endphp
                        <span class="badge {{ $colorClass }} text-white text-uppercase" style="font-size: 14px; padding: 6px 12px;">
                            {{ $statusLabel }}
                        </span>
                    </td>
                    <td class="align-middle text-center">
                        <x-button.show class="btn-icon" route="{{ route('purchases.show', $purchase) }}"/>
                        
                        @if ($purchase->status === \App\Enums\PurchaseStatus::PENDING)
                            <x-button.edit class="btn-icon" route="{{ route('purchases.edit', $purchase) }}"/>
                            <x-button.delete class="btn-icon" route="{{ route('purchases.delete', $purchase) }}"/>
                        @elseif ($purchase->status === \App\Enums\PurchaseStatus::APPROVED)
                            <x-button.edit class="btn-icon" route="{{ route('purchases.edit', $purchase) }}"/>
                        @endif
                        
                        <!-- Download Invoice -->
                        <a href="{{ route('purchases.download-invoice', $purchase) }}" 
                           class="btn btn-icon btn-outline-info" 
                           title="Download Invoice"
                           data-bs-toggle="tooltip">
                            <i class="fas fa-file-pdf"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td class="align-middle text-center" colspan="7">
                        No results found
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing <span>{{ $purchases->firstItem() ?? 0 }}</span>
            to <span>{{ $purchases->lastItem() ?? 0 }}</span> of <span>{{ $purchases->total() }}</span> entries
            @if($supplier)
                <span class="badge bg-primary ms-2">Filtered by: {{ $supplier->name }}</span>
            @endif
            @if($statusFilter)
                <span class="badge bg-info ms-2">Status Filter Active</span>
            @endif
            @if($dateFrom || $dateTo)
                <span class="badge bg-secondary ms-2">Date Filter Active</span>
            @endif
        </p>

        <ul class="pagination m-0 ms-auto">
        {{ $purchases->links() }}
        </ul>
    </div>
</div>
</div>
</div>
