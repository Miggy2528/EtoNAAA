@extends('layouts.butcher')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-box-open me-2"></i>{{ __('Product Details') }}
                </h2>
            </div>
        </div>
        @include('partials._breadcrumbs')
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-image me-2"></i>{{ __('Product Image') }}
                            </h3>
                        </div>
                        <div class="card-body">
                            <img class="img-account-profile mb-2" 
                                 src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/img/products/default.webp') }}" 
                                 alt="{{ $product->name }}" 
                                 id="image-preview" 
                                 style="width: 100%; height: auto; max-height: 400px; object-fit: contain; border: 1px solid #dee2e6; padding: 10px; border-radius: 8px; background-color: #fff;" />
                        </div>
                    </div>
                    
                    {{-- Meat Classification Card --}}
                    <div class="card mt-3">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Meat Classification
                            </h3>
                        </div>
                        <div class="card-body">
                            @if($product->meatCut)
                            <div class="meat-info">
                                <h5 class="mb-3">{{ $product->meatCut->name }}</h5>
                                
                                {{-- Classification Label --}}
                                <div class="classification-label mb-2">
                                    <small class="text-muted fw-bold">CLASSIFICATION:</small>
                                </div>
                                
                                <div class="classification-badges mb-3">
                                    {{-- Removed animal_type display as requested --}}
                                    @if($product->meatCut->meat_type)
                                    <span class="badge bg-primary me-1 mb-1">{{ ucfirst($product->meatCut->meat_type) }}</span>
                                    @endif
                                    @if($product->meatCut->quality)
                                    <span class="badge bg-success me-1 mb-1">{{ ucfirst($product->meatCut->quality) }}</span>
                                    @endif
                                    @if($product->meatCut->preparation_type)
                                    <span class="badge bg-warning text-dark me-1 mb-1">{{ ucfirst($product->meatCut->preparation_type) }}</span>
                                    @endif
                                    @if($product->meatCut->meat_subtype)
                                    <span class="badge bg-secondary me-1 mb-1">{{ ucfirst($product->meatCut->meat_subtype) }}</span>
                                    @endif
                                    @if($product->meatCut->quality_grade)
                                    <span class="badge bg-dark me-1 mb-1">{{ strtoupper($product->meatCut->quality_grade) }}</span>
                                    @endif
                                    @if($product->meatCut->preparation_style)
                                    <span class="badge bg-light text-dark me-1 mb-1">{{ ucfirst(str_replace('_', ' ', $product->meatCut->preparation_style)) }}</span>
                                    @endif
                                </div>
                                
                                <div class="classification-details">
                                    {{-- Removed animal_type display as requested --}}
                                    @if($product->meatCut->meat_type)
                                    <p class="mb-2"><strong>Meat Type:</strong> <span class="text-muted">{{ ucfirst($product->meatCut->meat_type) }}</span></p>
                                    @endif
                                    @if($product->meatCut->quality)
                                    <p class="mb-2"><strong>Quality:</strong> <span class="text-muted">{{ ucfirst($product->meatCut->quality) }}</span></p>
                                    @endif
                                    @if($product->meatCut->preparation_type)
                                    <p class="mb-2"><strong>Preparation:</strong> <span class="text-muted">{{ ucfirst($product->meatCut->preparation_type) }}</span></p>
                                    @endif
                                    @if($product->meatCut->meat_subtype)
                                    <p class="mb-2"><strong>Subtype:</strong> <span class="text-muted">{{ ucfirst($product->meatCut->meat_subtype) }}</span></p>
                                    @endif
                                    @if($product->meatCut->quality_grade)
                                    <p class="mb-2"><strong>Quality Grade:</strong> <span class="text-muted">{{ strtoupper($product->meatCut->quality_grade) }}</span></p>
                                    @endif
                                    @if($product->meatCut->preparation_style)
                                    <p class="mb-0"><strong>Prep Style:</strong> <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $product->meatCut->preparation_style)) }}</span></p>
                                    @endif
                                </div>
                            </div>
                            @else
                            <p class="text-muted">No meat cut information available</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-dark">
                                <i class="fas fa-box me-2"></i>{{ __('Product Details') }}
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                                <tbody>
                                    <tr>
                                        <td class="text-dark" style="width: 30%;"><strong>Name</strong></td>
                                        <td class="text-dark">{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Slug</strong></td>
                                        <td class="text-dark">{{ $product->slug }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Code</strong></td>
                                        <td class="text-dark">{{ $product->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Category</strong></td>
                                        <td>
                                            @if($product->category)
                                            <span class="badge bg-blue-lt text-dark">
                                                {{ $product->category->name }}
                                            </span>
                                            @else
                                            <span class="text-dark">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Unit</strong></td>
                                        <td>
                                            @if($product->unit)
                                            <span class="badge bg-blue-lt text-dark">
                                                {{ $product->unit->name ?? $product->unit->short_code }}
                                            </span>
                                            @else
                                            <span class="text-dark">N/A</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <tr>
                                        <td class="text-dark"><strong>Quantity</strong></td>
                                        <td>
                                            <span class="badge 
                                                @if($product->quantity <= 0) bg-danger
                                                @elseif($product->quantity <= $product->quantity_alert) bg-warning text-dark
                                                @else bg-success
                                                @endif">
                                                {{ $product->quantity }} {{ $product->unit->name ?? 'pcs' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Quantity Alert</strong></td>
                                        <td>
                                            <span class="badge bg-red-lt text-dark">
                                                {{ $product->quantity_alert }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Cost/Unit Price</strong></td>
                                        <td class="text-dark">₱{{ number_format($product->buying_price ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Price per KG</strong></td>
                                        <td class="text-dark">₱{{ number_format($product->price_per_kg ?? 0, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>Expiration Date</strong></td>
                                        <td class="text-dark">{{ $product->expiration_date ? $product->expiration_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark"><strong>{{ __('Notes') }}</strong></td>
                                        <td class="text-dark">{{ $product->notes ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="card-footer text-end">
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('products.edit', $product) }}" class="btn btn-primary">
                                <i class="fas fa-edit me-1"></i>{{ __('Edit') }}
                            </a>
                            @endif
                            
                            <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">
                                <i class="fas fa-arrow-left me-1"></i>{{ __('Back to Products') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .classification-badges .badge {
        font-size: 0.75rem;
    }
    
    .classification-details p {
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .classification-details p:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    
    table td {
        vertical-align: middle !important;
    }
    
    .classification-label .text-muted.fw-bold {
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .classification-badges {
        border-top: 1px dashed #dee2e6;
        padding-top: 1rem;
    }
</style>
@endpush