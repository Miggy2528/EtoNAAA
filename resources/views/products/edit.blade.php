@extends('layouts.butcher')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title">
                    {{ __('Edit Product') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('put')

                <div class="row">
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Image') }}
                                </h3>

                                <img
                                    class="img-account-profile mb-2"
                                    src="{{ $product->product_image ? asset('storage/products/'.$product->product_image) : asset('assets/img/products/default.webp') }}"
                                    id="image-preview"
                                    alt="{{ $product->name }}"
                                    style="width: 100%; height: auto; max-height: 400px; object-fit: contain; border: 1px solid #dee2e6; padding: 10px; border-radius: 8px; background-color: #fff;"
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
                    </div>

                    <div class="col-lg-8">

                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title">
                                    {{ __('Product Details') }}
                                </h3>

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
                                            >

                                            @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">
                                                Product category
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="category_id" id="category_id"
                                                    class="form-select @error('category_id') is-invalid @enderror"
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


                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label" for="unit_id">
                                                {{ __('Unit') }}
                                                <span class="text-danger">*</span>
                                            </label>

                                            <select name="unit_id" id="unit_id"
                                                    class="form-select @error('unit_id') is-invalid @enderror"
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

                                    <div class="col-sm-6 col-md-6">
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
                                            >

                                            @error('buying_price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-sm-6 col-md-6">
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
                                            >

                                            @error('quantity')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6 col-md-6">
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
                                                   placeholder="0"
                                                   value="{{ old('quantity_alert', $product->quantity_alert) }}"
                                            >

                                            @error('quantity_alert')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Meat Cut --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="meat_cut_id" class="form-label">
                                                Meat Cut <span class="text-danger">*</span>
                                            </label>
                                            <select name="meat_cut_id" id="meat_cut_id"
                                                    class="form-select @error('meat_cut_id') is-invalid @enderror">
                                                <option selected disabled>Select a meat cut:</option>
                                                @foreach ($meatCuts as $meatCut)
                                                    <option value="{{ $meatCut->id }}"
                                                        {{ old('meat_cut_id', $product->meat_cut_id) == $meatCut->id ? 'selected' : '' }}>
                                                        {{ $meatCut->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('meat_cut_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Price per KG --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="price_per_kg" class="form-label">Price per KG</label>
                                            <input type="number" 
                                                   name="price_per_kg" 
                                                   id="price_per_kg" 
                                                   class="form-control @error('price_per_kg') is-invalid @enderror" 
                                                   placeholder="0.00" 
                                                   value="{{ old('price_per_kg', $product->price_per_kg) }}" 
                                                   step="0.01" 
                                                   readonly>
                                            <small class="form-text text-muted">This will be automatically set based on the selected meat cut</small>
                                            @error('price_per_kg')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Source --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="source" class="form-label">Source</label>
                                            <input type="text" 
                                                   name="source" 
                                                   id="source" 
                                                   class="form-control @error('source') is-invalid @enderror" 
                                                   placeholder="e.g., Local Farm"
                                                   value="{{ old('source', $product->source) }}">
                                            @error('source')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    {{-- Expiration Date --}}
                                    <div class="col-sm-6 col-md-6">
                                        <div class="mb-3">
                                            <label for="expiration_date" class="form-label">Expiration Date</label>
                                            <input type="date" 
                                                   name="expiration_date" 
                                                   id="expiration_date" 
                                                   class="form-control @error('expiration_date') is-invalid @enderror" 
                                                   value="{{ old('expiration_date', $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '') }}">
                                            @error('expiration_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3 mb-0">
                                            <label for="notes" class="form-label">
                                                {{ __('Notes') }}
                                            </label>

                                            <textarea name="notes"
                                                      id="notes"
                                                      rows="5"
                                                      class="form-control @error('notes') is-invalid @enderror"
                                                      placeholder="Product notes"
                                            >{{ old('notes', $product->notes) }}</textarea>

                                            @error('notes')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>`
                                </div>
                            </div>

                            <div class="card-footer text-end">
                                <x-button.save type="submit">
                                    {{ __('Update') }}
                                </x-button.save>

                                <x-button.back route="{{ route('products.index') }}">
                                    {{ __('Cancel') }}
                                </x-button.back>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@pushonce('page-scripts')
    <script src="{{ asset('assets/js/img-preview.js') }}"></script>
    <script>
        // Auto-populate price per kg based on meat cut selection
        document.addEventListener('DOMContentLoaded', function() {
            const meatCutSelect = document.getElementById('meat_cut_id');
            const pricePerKgInput = document.getElementById('price_per_kg');
            
            // Meat cuts data with their default prices
            const meatCutsData = @json($meatCuts->pluck('default_price_per_kg', 'id'));
            
            console.log('Meat cuts data:', meatCutsData); // Debug log
            
            if (meatCutSelect && pricePerKgInput) {
                meatCutSelect.addEventListener('change', function() {
                    const selectedMeatCutId = this.value;
                    console.log('Selected meat cut ID:', selectedMeatCutId); // Debug log
                    
                    if (selectedMeatCutId && meatCutsData[selectedMeatCutId]) {
                        pricePerKgInput.value = parseFloat(meatCutsData[selectedMeatCutId]).toFixed(2);
                        console.log('Set price to:', pricePerKgInput.value); // Debug log
                    } else {
                        pricePerKgInput.value = '';
                    }
                });
                
                // Set initial value if meat cut is pre-selected
                if (meatCutSelect.value && meatCutsData[meatCutSelect.value]) {
                    pricePerKgInput.value = parseFloat(meatCutsData[meatCutSelect.value]).toFixed(2);
                    console.log('Initial price set to:', pricePerKgInput.value); // Debug log
                }
            } else {
                console.error('Required elements not found');
            }
        });
    </script>
@endpushonce
