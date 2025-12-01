@extends('layouts.butcher')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    <i class="fas fa-edit me-2"></i>{{ __('Edit Product') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" id="product-edit-form">
                @csrf
                @method('put')
                {{-- Debug information --}}
                <input type="hidden" name="_debug" value="1">

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-image me-2"></i>{{ __('Product Image') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                <img
                                    class="img-account-profile mb-2"
                                    src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.webp') }}"
                                    id="image-preview"
                                    alt="{{ $product->name }}"
                                    style="width: 100%; height: auto; max-height: 300px; object-fit: contain; border: 1px solid #dee2e6; padding: 10px; border-radius: 8px; background-color: #fff;"
                                >

                                <div class="small font-italic text-muted mb-2">
                                    JPG or PNG no larger than 2 MB
                                </div>

                                <input
                                    type="file"
                                    accept="image/*"
                                    id="image"
                                    name="product_image"
                                    class="form-control @error('product_image') is-invalid @enderror"
                                    onchange="previewImage();"
                                >

                                @error('product_image')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>
                        
                        {{-- Meat Classification Card --}}
                        <div class="card mt-3">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Meat Classification</h5>
                            </div>
                            <div class="card-body">
                                @if($product->meatCut)
                                <div class="meat-info">
                                    <h5>{{ $product->meatCut->name }}</h5>
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
                                        <p class="mb-1"><strong>Meat Type:</strong> {{ ucfirst($product->meatCut->meat_type) }}</p>
                                        @endif
                                        @if($product->meatCut->quality)
                                        <p class="mb-1"><strong>Quality:</strong> {{ ucfirst($product->meatCut->quality) }}</p>
                                        @endif
                                        @if($product->meatCut->preparation_type)
                                        <p class="mb-1"><strong>Preparation:</strong> {{ ucfirst($product->meatCut->preparation_type) }}</p>
                                        @endif
                                        @if($product->meatCut->meat_subtype)
                                        <p class="mb-1"><strong>Subtype:</strong> {{ ucfirst($product->meatCut->meat_subtype) }}</p>
                                        @endif
                                        @if($product->meatCut->quality_grade)
                                        <p class="mb-1"><strong>Quality Grade:</strong> {{ strtoupper($product->meatCut->quality_grade) }}</p>
                                        @endif
                                        @if($product->meatCut->preparation_style)
                                        <p class="mb-0"><strong>Prep Style:</strong> {{ ucfirst(str_replace('_', ' ', $product->meatCut->preparation_style)) }}</p>
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
                                <h3 class="card-title">
                                    <i class="fas fa-box me-2"></i>{{ __('Product Details') }}
                                </h3>
                                <div class="card-actions">
                                    <a href="{{ route('products.index') }}" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M18 6l-12 12"></path>
                                            <path d="M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row row-cards">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                {{ __('Name') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   id="name"
                                                   name="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   placeholder="Product name"
                                                   value="{{ old('name', $product->name) }}"
                                                   required
                                            >

                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Product category
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="category_id" id="category_id"
                                                    class="form-select @error('category_id') is-invalid @enderror"
                                                    required
                                            >
                                                <option selected="" disabled="">Select a category:</option>
                                                @foreach ($categories as $category)
                                                <option value="{{ $category->id }}" @if(old('category_id', $product->category_id) == $category->id) selected="selected" @endif>{{ $category->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('category_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="unit_id">
                                                {{ __('Unit') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="unit_id" id="unit_id"
                                                    class="form-select @error('unit_id') is-invalid @enderror"
                                                    required
                                            >
                                                <option selected="" disabled="">
                                                    Select a unit:
                                                </option>

                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}" @if(old('unit_id', $product->unit_id) == $unit->id) selected="selected" @endif>{{ $unit->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('unit_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="buying_price">
                                                Buying price
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="text"
                                                   id="buying_price"
                                                   name="buying_price"
                                                   class="form-control @error('buying_price') is-invalid @enderror"
                                                   placeholder="0"
                                                   value="{{ old('buying_price', $product->buying_price) }}"
                                                   required
                                            >

                                            @error('buying_price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity" class="form-label">
                                                {{ __('Quantity') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="number"
                                                   id="quantity"
                                                   name="quantity"
                                                   class="form-control @error('quantity') is-invalid @enderror"
                                                   min="0"
                                                   value="{{ old('quantity', $product->quantity) }}"
                                                   placeholder="0"
                                                   required
                                            >

                                            @error('quantity')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="quantity_alert" class="form-label">
                                                {{ __('Quantity Alert') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="number"
                                                   id="quantity_alert"
                                                   name="quantity_alert"
                                                   class="form-control @error('quantity_alert') is-invalid @enderror"
                                                   min="0"
                                                   value="{{ old('quantity_alert', $product->quantity_alert) }}"
                                                   placeholder="0"
                                                   required
                                            >

                                            @error('quantity_alert')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="price_per_kg" class="form-label">
                                                {{ __('Price Per KG') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <input type="number"
                                                   id="price_per_kg"
                                                   name="price_per_kg"
                                                   class="form-control @error('price_per_kg') is-invalid @enderror"
                                                   min="0"
                                                   step="0.01"
                                                   value="{{ old('price_per_kg', $product->price_per_kg) }}"
                                                   placeholder="0.00"
                                                   required
                                            >

                                            @error('price_per_kg')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="expiration_date" class="form-label">
                                                {{ __('Expiration Date') }}
                                            </label>

                                            <input type="date"
                                                   id="expiration_date"
                                                   name="expiration_date"
                                                   class="form-control @error('expiration_date') is-invalid @enderror"
                                                   value="{{ old('expiration_date', $product->expiration_date ? $product->expiration_date->format('Y-m-d') : null) }}"
                                            >

                                            @error('expiration_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="notes">{{ __('Notes') }}</label>
                                            <textarea name="notes" id="notes"
                                                      class="form-control @error('notes') is-invalid @enderror"
                                                      rows="3" placeholder="Product notes...">{{ old('notes', $product->notes) }}</textarea>
                                            @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <button type="submit" class="btn btn-primary" id="update-button">
                                    <i class="fas fa-save me-1"></i>{{ __('Update') }}
                                </button>
                                <a href="{{ route('products.index') }}" class="btn btn-secondary ms-2">
                                    <i class="fas fa-times me-1"></i>{{ __('Cancel') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-scripts')
<script>
    // Add form submission debugging
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('product-edit-form');
        const updateButton = document.getElementById('update-button');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                console.log('Form is being submitted');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);
                // Don't prevent default, just log for debugging
            });
        }
        
        if (updateButton) {
            updateButton.addEventListener('click', function(e) {
                console.log('Update button clicked');
                // Don't prevent default, just log for debugging
            });
        }
    });
    
    function previewImage() {
        const image = document.querySelector('#image');
        const imgPreview = document.querySelector('#image-preview');

        imgPreview.style.display = 'block';

        const oFReader = new FileReader();
        oFReader.readAsDataURL(image.files[0]);

        oFReader.onload = function (oFREvent) {
            imgPreview.src = oFREvent.target.result;
        }
    }
</script>
@endpush