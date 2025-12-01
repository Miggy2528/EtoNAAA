<div class="card">
    <div class="card-body border-bottom py-3">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="row g-2">
                    {{-- Search Bar --}}
                    <div class="col-md-3 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Search</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Search invoice or customer...">
                        </div>
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Status</label>
                        <select wire:model.live="statusFilter" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="for_delivery">For Delivery</option>
                            <option value="complete">Complete</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    {{-- Payment Type Filter --}}
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Payment Type</label>
                        <select wire:model.live="paymentFilter" class="form-select form-select-sm">
                            <option value="">All Payments</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Date From</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control form-control-sm">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-2 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Date To</label>
                        <input type="date" wire:model.live="dateTo" class="form-control form-control-sm">
                    </div>

                    {{-- Per Page --}}
                    <div class="col-md-1 col-sm-6">
                        <label class="form-label text-secondary fw-bold">Show</label>
                        <select wire:model.live="perPage" class="form-select form-select-sm">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-spinner.loading-spinner/>

    <div class="table-responsive" wire:loading.remove>
        @php
            $pendingOrders = $orders->where('order_status', \App\Enums\OrderStatus::PENDING);
            $forDeliveryOrders = $orders->where('order_status', \App\Enums\OrderStatus::FOR_DELIVERY);
            $completeOrders = $orders->where('order_status', \App\Enums\OrderStatus::COMPLETE);
            $cancelledOrders = $orders->where('order_status', \App\Enums\OrderStatus::CANCELLED);
        @endphp

        @if($orders->count() > 0)
            {{-- Pending Orders Section --}}
            @if($pendingOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-warning bg-opacity-10 border-start border-warning border-4 p-3 mb-3 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-warning">
                                <i class="fas fa-clock me-2"></i>
                                Pending Orders ({{ $pendingOrders->count() }})
                            </h5>
                            <span class="badge bg-warning text-dark fs-6">{{ $pendingOrders->count() }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover card-table table-vcenter text-nowrap datatable">
                            <thead class="table-warning">
                                <tr>
                                    <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Customer') }}</th>
                                    <th class="align-middle text-center">{{ __('Date') }}</th>
                                    <th class="align-middle text-center">{{ __('Payment') }}</th>
                                    <th class="align-middle text-center">{{ __('Total') }}</th>
                                    <th class="align-middle text-center">{{ __('Status') }}</th>
                                    <th class="align-middle text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendingOrders as $order)
                                    <tr>
                                        <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center fw-bold text-primary">{{ $order->invoice_no }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $order->customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="d-none d-md-inline">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</span>
                                            <span class="d-inline d-md-none">{{ $order->created_at->timezone('Asia/Manila')->format('d/m/y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-primary">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                        <td class="align-middle text-center fw-bold text-success">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="align-middle text-center">
                                            <x-status dot color="orange" class="text-uppercase">
                                                {{ $order->order_status->label() }}
                                            </x-status>
                                        </td>
                                        <td class="align-middle text-center" style="width: 5%">
                                            <div class="btn-group btn-group-sm gap-1" role="group">
                                                <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                                <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                                
                                                @if(auth()->user()->isAdmin())
                                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove" onclick="return confirm('Are you sure you want to remove this order?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- For Delivery Orders Section --}}
            @if($forDeliveryOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-primary bg-opacity-10 border-start border-primary border-4 p-3 mb-3 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-primary">
                                <i class="fas fa-truck me-2"></i>
                                For Delivery Orders ({{ $forDeliveryOrders->count() }})
                            </h5>
                            <span class="badge bg-primary fs-6">{{ $forDeliveryOrders->count() }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover card-table table-vcenter text-nowrap datatable">
                            <thead class="table-primary">
                                <tr>
                                    <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Customer') }}</th>
                                    <th class="align-middle text-center">{{ __('Date') }}</th>
                                    <th class="align-middle text-center">{{ __('Payment') }}</th>
                                    <th class="align-middle text-center">{{ __('Total') }}</th>
                                    <th class="align-middle text-center">{{ __('Status') }}</th>
                                    <th class="align-middle text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forDeliveryOrders as $order)
                                    <tr>
                                        <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center fw-bold text-primary">{{ $order->invoice_no }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $order->customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="d-none d-md-inline">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</span>
                                            <span class="d-inline d-md-none">{{ $order->created_at->timezone('Asia/Manila')->format('d/m/y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-primary">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                        <td class="align-middle text-center fw-bold text-success">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="align-middle text-center">
                                            <x-status dot color="blue" class="text-uppercase">
                                                {{ $order->order_status->label() }}
                                            </x-status>
                                        </td>
                                        <td class="align-middle text-center" style="width: 5%">
                                            <div class="btn-group btn-group-sm gap-1" role="group">
                                                <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                                <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                                @if(auth()->user()->isAdmin())
                                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove" onclick="return confirm('Are you sure you want to remove this order?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Complete Orders Section --}}
            @if($completeOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 border-start border-success border-4 p-3 mb-3 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                Complete Orders ({{ $completeOrders->count() }})
                            </h5>
                            <span class="badge bg-success fs-6">{{ $completeOrders->count() }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover card-table table-vcenter text-nowrap datatable">
                            <thead class="table-success">
                                <tr>
                                    <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Customer') }}</th>
                                    <th class="align-middle text-center">{{ __('Date') }}</th>
                                    <th class="align-middle text-center">{{ __('Payment') }}</th>
                                    <th class="align-middle text-center">{{ __('Total') }}</th>
                                    <th class="align-middle text-center">{{ __('Status') }}</th>
                                    <th class="align-middle text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($completeOrders as $order)
                                    <tr>
                                        <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center fw-bold text-primary">{{ $order->invoice_no }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $order->customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="d-none d-md-inline">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</span>
                                            <span class="d-inline d-md-none">{{ $order->created_at->timezone('Asia/Manila')->format('d/m/y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-primary">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                        <td class="align-middle text-center fw-bold text-success">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="align-middle text-center">
                                            <x-status dot color="green" class="text-uppercase">
                                                {{ $order->order_status->label() }}
                                            </x-status>
                                        </td>
                                        <td class="align-middle text-center" style="width: 5%">
                                            <div class="btn-group btn-group-sm gap-1" role="group">
                                                <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                                <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                                @if(auth()->user()->isAdmin())
                                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove" onclick="return confirm('Are you sure you want to remove this order?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Cancelled Orders Section --}}
            @if($cancelledOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 border-start border-danger border-4 p-3 mb-3 rounded shadow-sm">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-danger">
                                <i class="fas fa-times-circle me-2"></i>
                                Cancelled Orders ({{ $cancelledOrders->count() }})
                            </h5>
                            <span class="badge bg-danger fs-6">{{ $cancelledOrders->count() }}</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover card-table table-vcenter text-nowrap datatable">
                            <thead class="table-danger">
                                <tr>
                                    <th class="align-middle text-center w-1">{{ __('No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Invoice No.') }}</th>
                                    <th class="align-middle text-center">{{ __('Customer') }}</th>
                                    <th class="align-middle text-center">{{ __('Date') }}</th>
                                    <th class="align-middle text-center">{{ __('Payment') }}</th>
                                    <th class="align-middle text-center">{{ __('Total') }}</th>
                                    <th class="align-middle text-center">{{ __('Cancel Reason') }}</th>
                                    <th class="align-middle text-center">{{ __('Status') }}</th>
                                    <th class="align-middle text-center">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cancelledOrders as $order)
                                    <tr>
                                        <td class="align-middle text-center">{{ $loop->iteration }}</td>
                                        <td class="align-middle text-center fw-bold text-primary">{{ $order->invoice_no }}</td>
                                        <td class="align-middle">
                                            <div class="d-flex align-items-center">
                                                <span>{{ $order->customer->name }}</span>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="d-none d-md-inline">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</span>
                                            <span class="d-inline d-md-none">{{ $order->created_at->timezone('Asia/Manila')->format('d/m/y') }}</span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <span class="badge bg-primary">{{ ucfirst($order->payment_type) }}</span>
                                        </td>
                                        <td class="align-middle text-center fw-bold text-success">₱{{ number_format($order->total, 2) }}</td>
                                        <td class="align-middle text-center">
                                            <span class="text-danger d-none d-md-inline">
                                                {{ $order->cancellation_reason ?? 'No reason provided' }}
                                            </span>
                                            <span class="text-danger d-inline d-md-none">
                                                {{ substr($order->cancellation_reason ?? 'No reason', 0, 15) }}@if(strlen($order->cancellation_reason ?? 'No reason') > 15)...@endif
                                            </span>
                                        </td>
                                        <td class="align-middle text-center">
                                            <x-status dot color="red" class="text-uppercase">
                                                {{ $order->order_status->label() }}
                                            </x-status>
                                        </td>
                                        <td class="align-middle text-center" style="width: 5%">
                                            <div class="btn-group btn-group-sm gap-1" role="group">
                                                <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                                <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                                <a href="{{ route('orders.edit-invoice', $order->id) }}" class="btn btn-icon btn-primary" title="Edit Invoice">
                                                    <i class="fas fa-edit text-white"></i>
                                                </a>
                                                @if(auth()->user()->isAdmin())
                                                <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove" onclick="return confirm('Are you sure you want to remove this order?');">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="fas fa-box-open" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <p class="empty-title h4">No orders found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="card-footer d-flex align-items-center flex-column flex-md-row">
        <p class="m-0 text-secondary mb-2 mb-md-0">
            Showing <span>{{ $orders->firstItem() }}</span> to <span>{{ $orders->lastItem() }}</span> of <span>{{ $orders->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-md-auto">
            {{ $orders->links() }}
        </ul>
    </div>
</div> 