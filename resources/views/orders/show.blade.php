@extends('layouts.butcher')

@push('page-styles')
<style>
    .cursor-pointer:hover {
        opacity: 0.8;
        transform: scale(1.02);
        transition: all 0.2s ease-in-out;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }
    
    .status-badge {
        font-size: 0.95rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        display: inline-block;
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
    <div class="container-xl">

        {{-- Order Header --}}
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="page-title">{{ __('Order Details') }}</h1>
                <div class="d-flex gap-2">
                    <x-back-button url="{{ route('orders.index') }}" text="Back to Orders" />
                    <!-- Remove button -->
                    <form action="{{ route('orders.destroy', $order) }}" method="POST">
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
                </div>
            </div>
            <p class="text-muted">Invoice: <strong>{{ $order->invoice_no }}</strong> | Customer: <strong>{{ $order->customer->name }}</strong></p>
        </div>

        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">{{ __('Order Summary') }}</h3>
                <x-action.close route="{{ route('orders.index') }}"/>
            </div>

            <div class="card-body">

                {{-- Basic Order Info --}}
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Order Date') }}</label>
                        <input type="text" class="form-control" value="{{ $order->created_at->timezone('Asia/Manila')->format('d-m-Y g:i A') }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Payment Type') }}</label>
                        <input type="text" class="form-control" value="{{ $order->payment_type }}" disabled>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Status') }}</label>
                        <div>
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
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Customer Account') }}</label>
                        <input type="text" class="form-control" value="{{ $order->customer->name }}" disabled>
                    </div>
                    @if($order->receiver_name)
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Receiver Name') }}</label>
                        <input type="text" class="form-control" value="{{ $order->receiver_name }}" disabled>
                    </div>
                    @endif
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Customer Email') }}</label>
                        <input type="text" class="form-control" value="{{ $order->customer_email }}" disabled>
                    </div>
                </div>

                {{-- Delivery Info --}}
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4 class="mb-3">{{ __('Delivery Information') }}</h4>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('City') }}</label>
                        <input type="text" class="form-control" value="{{ $order->city }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Postal Code') }}</label>
                        <input type="text" class="form-control" value="{{ $order->postal_code }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Barangay') }}</label>
                        <input type="text" class="form-control" value="{{ $order->barangay }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Street Name') }}</label>
                        <input type="text" class="form-control" value="{{ $order->street_name }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Building') }}</label>
                        <input type="text" class="form-control" value="{{ $order->building ?? 'N/A' }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('House No.') }}</label>
                        <input type="text" class="form-control" value="{{ $order->house_no ?? 'N/A' }}" disabled>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">{{ __('Full Delivery Address') }}</label>
                        <input type="text" class="form-control" value="{{ $order->delivery_address }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Contact Number') }}</label>
                        <input type="text" class="form-control" value="{{ $order->contact_phone }}" disabled>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">{{ __('Delivery Notes') }}</label>
                        <textarea class="form-control" rows="2" disabled>{{ $order->delivery_notes }}</textarea>
                    </div>
                </div>

                {{-- GCash Info --}}
                <div class="row mb-4">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('GCash Reference') }}</label>
                        <input type="text" class="form-control" value="{{ $order->gcash_reference }}" disabled>
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">{{ __('Proof of Payment') }}</label>
                        <div class="border p-2 rounded bg-light text-center">
                            @if ($order->proof_of_payment)
                                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                                     alt="Proof of Payment" 
                                     class="img-fluid cursor-pointer" 
                                     style="max-height: 200px; cursor: pointer;" 
                                     data-bs-toggle="modal" 
                                     data-bs-target="#proofOfPaymentModal"
                                     title="Click to view full image">
                                <div class="mt-2">
                                    <small class="text-muted"><i class="ti ti-click"></i> Click image to enlarge</small>
                                </div>
                            @else
                                <span class="text-muted">{{ __('No image uploaded') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                
                {{-- Shop Information --}}
                <div class="row mb-4">
                    <div class="col-12 mb-3">
                        <h4 class="mb-3">{{ __('From Yannis Meat Shop') }}</h4>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Address') }}</label>
                        <input type="text" class="form-control" value="Katapatn Rd, 17, Cabuyao City, 4025 Laguna" disabled>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Phone') }}</label>
                        <input type="text" class="form-control" value="+63 09082413347" disabled>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="text" class="form-control" value="email@example.com" disabled>
                    </div>
                </div>

                {{-- Products Table --}}
                <div class="table-responsive mb-4">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Photo</th>
                                <th>Product Name</th>
                                <th>Code</th>
                                <th class="text-center">Quantity</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->details as $item)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-center">
                                    <img src="{{ $item->product->product_image ? asset('storage/products/'.$item->product->product_image) : asset('assets/img/products/default.webp') }}" class="img-thumbnail" style="max-height: 80px;">
                                </td>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->product->code }}</td>
                                <td class="text-center">{{ $item->quantity }} {{ $item->product->unit->name ?? 'kg' }}</td>
                                <td class="text-end">₱{{ number_format($item->unitcost, 2) }}/{{ $item->product->unit->name ?? 'kg' }}</td>
                                <td class="text-end">₱{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Paid Amount</td>
                                <td class="text-end fw-bold">{{ number_format($order->pay, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-end fw-bold">Due</td>
                                <td class="text-end fw-bold">{{ number_format($order->due, 2) }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="6" class="text-end fw-bold">Total</td>
                                <td class="text-end fw-bold">{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

            {{-- Footer Action --}}
            @if ($order->order_status === \App\Enums\OrderStatus::PENDING)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        Update order status using the buttons below
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $order->id }}, 'Cancelled')">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="updateStatus({{ $order->id }}, 'For Delivery')">
                            <i class="ti ti-truck me-1"></i>For Delivery
                        </button>
                        <button class="btn btn-success btn-sm" onclick="updateStatus({{ $order->id }}, 'Completed')">
                            <i class="ti ti-check me-1"></i>Complete Order
                        </button>
                    </div>
                </div>
            </div>
            @endif
            @if ($order->order_status === \App\Enums\OrderStatus::FOR_DELIVERY)
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        Order is out for delivery. Complete when finished.
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-danger btn-sm" onclick="updateStatus({{ $order->id }}, 'Cancelled')">
                            <i class="ti ti-x me-1"></i>Cancel
                        </button>
                        <button class="btn btn-success btn-sm" onclick="updateStatus({{ $order->id }}, 'Completed')">
                            <i class="ti ti-check me-1"></i>Complete Order
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Cancel Order Modal --}}
@if ($order->order_status === \App\Enums\OrderStatus::PENDING)
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="cancelOrderModalLabel">
                    <i class="ti ti-alert-triangle me-2"></i>
                    Cancel Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-alert-triangle fs-1 me-3"></i>
                            <div>
                                <h6 class="alert-heading mb-1">Important Warning</h6>
                                <p class="mb-0">Are you sure you want to cancel this order? This action cannot be undone and will:</p>
                                <ul class="mb-0 mt-2">
                                    <li>Restore product quantities to inventory</li>
                                    <li>Notify the customer about the cancellation</li>
                                    <li>Update the order status permanently</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Customer Account:</strong> {{ $order->customer->name }}
                        </div>
                        <div class="col-md-6">
                            <strong>Order Date:</strong> {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <strong>Payment Type:</strong> {{ ucfirst($order->payment_type) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Total Amount:</strong> ₱{{ number_format($order->total, 2) }}
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label fw-bold">
                            Cancellation Reason <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" 
                                  id="cancellation_reason" 
                                  name="cancellation_reason" 
                                  rows="4" 
                                  required 
                                  placeholder="Please provide a detailed reason for cancelling this order. This will be sent to the customer..."></textarea>
                        <div class="form-text">This reason will be visible to the customer in their notification.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Close
                    </button>
                    <button type="submit" class="btn btn-danger" id="confirmCancel">
                        <i class="ti ti-ban me-1"></i>Cancel Order
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
// Prevent modal conflicts and improve UX
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cancelOrderModal');
    if (modal) {
        // Clear form when modal is hidden
        modal.addEventListener('hidden.bs.modal', function() {
            const form = modal.querySelector('form');
            if (form) {
                form.reset();
                const textarea = form.querySelector('textarea[name="cancellation_reason"]');
                if (textarea) {
                    textarea.classList.remove('is-invalid');
                }
            }
        });
        
        // Add confirmation before form submission
        const confirmButton = document.getElementById('confirmCancel');
        if (confirmButton) {
            confirmButton.addEventListener('click', function(e) {
                const textarea = document.getElementById('cancellation_reason');
                if (!textarea.value.trim()) {
                    e.preventDefault();
                    textarea.focus();
                    textarea.classList.add('is-invalid');
                    return false;
                }
                textarea.classList.remove('is-invalid');
            });
        }
    }
});
</script>
@endpush
@endif

{{-- Proof of Payment Modal --}}
@if ($order->proof_of_payment)
<div class="modal fade" id="proofOfPaymentModal" tabindex="-1" aria-labelledby="proofOfPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofOfPaymentModalLabel">
                    <i class="ti ti-photo me-2"></i>Proof of Payment - Order #{{ $order->invoice_no }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center p-4" style="background-color: #f8f9fa;">
                <img src="{{ asset('storage/' . $order->proof_of_payment) }}" 
                     alt="Proof of Payment" 
                     class="img-fluid rounded shadow" 
                     style="max-width: 100%; max-height: 80vh; object-fit: contain;">
                <div class="mt-3">
                    <p class="text-muted mb-1"><strong>GCash Reference:</strong> {{ $order->gcash_reference ?? 'N/A' }}</p>
                    <p class="text-muted mb-0"><strong>Order Date:</strong> {{ $order->created_at->timezone('Asia/Manila')->format('M d, Y g:i A') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ asset('storage/' . $order->proof_of_payment) }}" 
                   target="_blank" 
                   class="btn btn-primary">
                    <i class="ti ti-external-link me-1"></i>Open in New Tab
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>Close
                </button>
            </div>
        </div>
    </div>
</div>
@endif

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
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the order status.');
        });
    }
</script>
@endsection