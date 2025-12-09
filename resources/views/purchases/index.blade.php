@extends('layouts.butcher')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title mb-1">
                            <i class="fas fa-shopping-bag me-2"></i>
                            @if(request()->has('supplier_id'))
                                Supplier Purchase Orders
                            @else
                                Purchase Management
                            @endif
                        </h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                @if(request()->has('supplier_id'))
                                    <li class="breadcrumb-item"><a href="{{ route('suppliers.index') }}">Suppliers</a></li>
                                    @php
                                        $supplier = \App\Models\Supplier::find(request()->get('supplier_id'));
                                    @endphp
                                    @if($supplier)
                                        <li class="breadcrumb-item"><a href="{{ route('suppliers.show', $supplier) }}">{{ $supplier->name }}</a></li>
                                    @endif
                                @endif
                                <li class="breadcrumb-item active" aria-current="page">Purchases</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        
        <x-alert/>
        
        @if($purchases->isEmpty() && !request()->has('supplier_id'))
        <x-empty
            title="No purchases found"
            message="Try adjusting your search or filter to find what you're looking for."
            button_label="{{ __('Add your first Purchase') }}"
            button_route="{{ route('purchases.create') }}"
        />
        @else
        @livewire('tables.purchase-table')
        @endif
    </div>
</div>
@endsection
