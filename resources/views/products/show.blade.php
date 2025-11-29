@extends('layouts.butcher')

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl mb-3">
        <div class="row g-2 align-items-center mb-3">
            <div class="col">
                <h2 class="page-title text-dark">
                    {{ __('Product Details') }}
                </h2>
            </div>
        </div>

        @include('partials._breadcrumbs', ['model' => $product])
    </div>
</div>

<div class="page-body">
    <div class="container-xl">
        <div class="row row-cards">

            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h3 class="card-title text-dark">
                                {{ __('Product Image') }}
                            </h3>

                            <img class="img-account-profile mb-2" 
                                 src="{{ $product->product_image ? asset('storage/products/' . $product->product_image) : asset('assets/img/products/default.webp') }}" 
                                 alt="{{ $product->name }}" 
                                 id="image-preview" 
                                 style="width: 100%; height: auto; max-height: 400px; object-fit: contain; border: 1px solid #dee2e6; padding: 10px; border-radius: 8px; background-color: #fff;" />
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title text-dark">
                                {{ __('Product Details') }}
                            </h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                                <tbody>
                                    <tr>
                                        <td class="text-dark">Name</td>
                                        <td class="text-dark">{{ $product->name }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Slug</td>
                                        <td class="text-dark">{{ $product->slug }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Code</td>
                                        <td class="text-dark">{{ $product->code }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Category</td>
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
                                        <td class="text-dark">Unit</td>
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
                                        <td class="text-dark">Quantity</td>
                                        <td class="text-dark">{{ $product->quantity }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Quantity Alert</td>
                                        <td>
                                            
                                            <span class="badge bg-red-lt text-dark">
                                                {{ $product->quantity_alert }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Cost/Unit Price</td>
                                        <td class="text-dark">{{ $product->buying_price }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Price per KG</td>
                                        <td class="text-dark">{{ $product->price_per_kg }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">Expiration Date</td>
                                        <td class="text-dark">{{ $product->expiration_date ? $product->expiration_date->format('M d, Y') : 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-dark">{{ __('Notes') }}</td>
                                        <td class="text-dark">{{ $product->notes }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                      <div class="card-footer text-end" style="color: white;">
                      @if(auth()->user()->isAdmin())
                  <x-button.edit route="{{ route('products.edit', $product) }}" style="color: white;">
                            {{ __('Edit') }}
                          </x-button.edit>
                            @endif
                            
                            <x-button.back route="{{ route('products.index') }}">
                                {{ __('Cancel') }}
                            </x-button.back>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
