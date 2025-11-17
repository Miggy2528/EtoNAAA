@extends('layouts.butcher')

@push('page-styles')
<style>
    .status-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
    }

    .status-for-delivery {
        background-color: #cfe2ff;
        color: #084298;
    }

    .status-complete {
        background-color: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background-color: #f8d7da;
        color: #721c24;
    }
</style>
@endpush

@section('content')
<div class="page-body">
    @if($orders->isEmpty())
    <div class="empty">
        <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <circle cx="12" cy="12" r="9" />
                <line x1="9" y1="10" x2="9.01" y2="10" />
                <line x1="15" y1="10" x2="15.01" y2="10" />
                <path d="M9.5 15.25a3.5 3.5 0 0 1 5 0" />
            </svg>
        </div>
        <p class="empty-title">
            No orders found
        </p>
        <p class="empty-subtitle text-secondary">
            Try adjusting your search or filter to find what you're looking for.
        </p>
        <div class="empty-action">
            <a href="{{ route('orders.create') }}" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 5l0 14"></path><path d="M5 12l14 0"></path></svg>
                Add your first Order
            </a>
        </div>
    </div>
    @else
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Orders: Completed') }}
                    </h3>
                </div>

                <div class="card-actions">
                    <a href="{{ route('purchases.create') }}" class="btn btn-icon btn-outline-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">{{ __('No.') }}</th>
                            <th scope="col" class="text-center">{{ __('Invoice No.') }}</th>
                            <th scope="col" class="text-center">{{ __('Customer') }}</th>
                            <th scope="col" class="text-center">{{ __('Date') }}</th>
                            <th scope="col" class="text-center">{{ __('Payment') }}</th>
                            <th scope="col" class="text-center">{{ __('Total') }}</th>
                            <th scope="col" class="text-center">{{ __('Status') }}</th>
                            <th scope="col" class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                        <tr>
                            <td class="text-center">{{ $loop->iteration  }}</td>
                            <td class="text-center">{{ $order->invoice_no }}</td>
                            <td class="text-center">{{ $order->customer->name }}</td>
                            <td class="text-center">{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}</td>
                            <td class="text-center">{{ $order->payment_type }}</td>
                            <td class="text-center">₱{{ number_format($order->total, 2) }}</td>
                            <td class="text-center">
                                @php
                                    $statusClass = match($order->order_status) {
                                        \App\Enums\OrderStatus::PENDING => 'status-pending',
                                        \App\Enums\OrderStatus::FOR_DELIVERY => 'status-for-delivery',
                                        \App\Enums\OrderStatus::COMPLETE => 'status-complete',
                                        \App\Enums\OrderStatus::CANCELLED => 'status-cancelled',
                                        default => 'status-pending'
                                    };
                                @endphp
                                <span class="badge status-badge {{ $statusClass }}">
                                    {{ $order->order_status->label() }}
                                </span>
                            </td>
                            <td class="text-center">
                                <!-- Action Buttons -->
                                <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $order->id }}, 'Cancelled')">Cancel</button>
                                <button class="btn btn-warning btn-sm" onclick="updateStatus({{ $order->id }}, 'For Delivery')">For Delivery</button>
                                
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-icon" style="background-color: #8B0000; border-color: #8B0000;" onmouseover="this.style.backgroundColor='#A52A2A'; this.style.borderColor='#A52A2A';" onmouseout="this.style.backgroundColor='#8B0000'; this.style.borderColor='#8B0000';">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                </a>
                                <a href="{{ route('orders.show', $order) }}" class="btn btn-icon" style="background-color: #8B0000; border-color: #8B0000;" onmouseover="this.style.backgroundColor='#A52A2A'; this.style.borderColor='#A52A2A';" onmouseout="this.style.backgroundColor='#8B0000'; this.style.borderColor='#8B0000';">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" /><path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" /></svg>
                                </a>
                                <a href="{{ route('order.downloadInvoice', $order) }}" class="btn btn-icon btn-primary" onmouseover="this.style.opacity='0.8';" onmouseout="this.style.opacity='1';">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="white" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" /><path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" /><path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" /></svg>
                                </a>
                                <!-- Remove button instead of delete -->
                                <form action="{{ route('orders.destroy', $order) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-icon btn-outline-danger" onclick="return confirm('Are you sure you want to remove this order?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7l16 0" />
                                            <path d="M10 11l0 6" />
                                            <path d="M14 11l0 6" />
                                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                {{--- ---}}
            </div>
        </div>
    </div>
    @endif
</div>

<script>
    function updateStatus(orderId, status) {
        if (!confirm(`Are you sure you want to mark this order as ${status}?`)) {
            return;
        }
        
        fetch(`/admin/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status })
        }).then(res => res.json()).then(data => {
            if(data.success) {
                // Show success message
                alert(data.message);
                // Reload the page to reflect changes
                location.reload();
            } else {
                // Show error message
                alert('Error: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status.');
        });
    }
</script>
@endsection