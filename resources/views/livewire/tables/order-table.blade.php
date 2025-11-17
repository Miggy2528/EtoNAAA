<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">
                {{ __('Orders') }}
            </h3>
        </div>

        <div class="card-actions">
            <x-action.create route="{{ route('orders.create') }}" />
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="row g-2">
                    {{-- Search Bar --}}
                    <div class="col-md-3">
                        <label class="form-label text-secondary">Search</label>
                        <input type="text" wire:model.live="search" class="form-control form-control-sm" placeholder="Search invoice or customer...">
                    </div>

                    {{-- Status Filter --}}
                    <div class="col-md-2">
                        <label class="form-label text-secondary">Status</label>
                        <select wire:model.live="statusFilter" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="for_delivery">For Delivery</option>
                            <option value="complete">Complete</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>

                    {{-- Payment Type Filter --}}
                    <div class="col-md-2">
                        <label class="form-label text-secondary">Payment Type</label>
                        <select wire:model.live="paymentFilter" class="form-select form-select-sm">
                            <option value="">All Payments</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                        </select>
                    </div>

                    {{-- Date From --}}
                    <div class="col-md-2">
                        <label class="form-label text-secondary">Date From</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control form-control-sm">
                    </div>

                    {{-- Date To --}}
                    <div class="col-md-2">
                        <label class="form-label text-secondary">Date To</label>
                        <input type="date" wire:model.live="dateTo" class="form-control form-control-sm">
                    </div>

                    {{-- Per Page --}}
                    <div class="col-md-1">
                        <label class="form-label text-secondary">Show</label>
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
                    <div class="bg-warning bg-opacity-10 border-start border-warning border-4 p-3 mb-3">
                        <h5 class="mb-0 text-warning">
                            <i class="ti ti-clock me-2"></i>
                            Pending Orders ({{ $pendingOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
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
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="orange" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove">
                                                <i class="ti ti-trash"></i>remove
                                            </button>
                                        </form>
                                        @endif

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- For Delivery Orders Section --}}
            @if($forDeliveryOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-primary bg-opacity-10 border-start border-primary border-4 p-3 mb-3">
                        <h5 class="mb-0 text-primary">
                            <i class="ti ti-truck me-2"></i>
                            For Delivery Orders ({{ $forDeliveryOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
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
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="blue" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove">
                                                <i class="ti ti-trash"></i>remove
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Complete Orders Section --}}
            @if($completeOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-success bg-opacity-10 border-start border-success border-4 p-3 mb-3">
                        <h5 class="mb-0 text-success">
                            <i class="ti ti-check me-2"></i>
                            Complete Orders ({{ $completeOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
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
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="green" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove">
                                                <i class="ti ti-trash"></i>remove
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Cancelled Orders Section --}}
            @if($cancelledOrders->count() > 0)
                <div class="mb-4">
                    <div class="bg-danger bg-opacity-10 border-start border-danger border-4 p-3 mb-3">
                        <h5 class="mb-0 text-danger">
                            <i class="ti ti-x me-2"></i>
                            Cancelled Orders ({{ $cancelledOrders->count() }})
                        </h5>
                    </div>
                    <table class="table table-bordered card-table table-vcenter text-nowrap">
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
                                    <td class="align-middle text-center">{{ $order->invoice_no }}</td>
                                    <td class="align-middle">{{ $order->customer->name }}</td>
                                    <td class="align-middle text-center">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</td>
                                    <td class="align-middle text-center">{{ $order->payment_type }}</td>
                                    <td class="align-middle text-center">₱{{ number_format($order->total, 2) }}</td>
                                    <td class="align-middle text-center">
                                        <span class="text-danger">
                                            {{ $order->cancellation_reason ?? 'No reason provided' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <x-status dot color="red" class="text-uppercase">
                                            {{ $order->order_status->label() }}
                                        </x-status>
                                    </td>
                                    <td class="align-middle text-center" style="width: 5%">
                                        <x-button.show class="btn-icon" route="{{ route('orders.show', $order) }}"/>
                                        <x-button.print class="btn-icon" route="{{ route('order.downloadInvoice', $order) }}"/>
                                        @if(auth()->user()->isAdmin())
                                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to remove this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon btn-outline-danger" title="Remove">
                                                <i class="ti ti-trash"></i>remove
                                            </button>
                                        </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <div class="empty">
                    <div class="empty-icon">
                        <i class="ti ti-package-off" style="font-size: 3rem; color: #6c757d;"></i>
                    </div>
                    <p class="empty-title">No orders found</p>
                    <p class="empty-subtitle text-muted">
                        Try adjusting your search or filter to find what you're looking for.
                    </p>
                </div>
            </div>
        @endif
    </div>

    <div class="card-footer d-flex align-items-center">
        <p class="m-0 text-secondary">
            Showing <span>{{ $orders->firstItem() }}</span> to <span>{{ $orders->lastItem() }}</span> of <span>{{ $orders->total() }}</span> entries
        </p>

        <ul class="pagination m-0 ms-auto">
            {{ $orders->links() }}
        </ul>
    </div>
</div>