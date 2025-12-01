<div>
    <div class="table-responsive">
        <table class="table table-bordered table-vcenter card-table table-hover">
            <thead class="thead-dark">
                <tr>
                    <th class="align-middle text-center d-none d-md-table-cell" style="width: 25%">
                        <i class="fas fa-box me-1"></i>Product
                    </th>
                    <th class="align-middle text-center" style="width: 15%">
                        <i class="fas fa-box me-1"></i>Product
                    </th>
                    <th class="align-middle text-center" style="width: 12%">
                        <i class="fas fa-sort-numeric-up me-1"></i>Quantity
                    </th>
                    <th class="align-middle text-center d-none d-sm-table-cell" style="width: 12%">
                        <i class="fas fa-tag me-1"></i>Price
                    </th>
                    <th class="align-middle text-center" style="width: 12%">
                        <i class="fas fa-receipt me-1"></i>Total
                    </th>
                    <th class="align-middle text-center" style="width: 12%">
                        <i class="fas fa-cog me-1"></i>Actions
                    </th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoiceProducts as $index => $invoiceProduct)
                <tr class="{{ $invoiceProduct['is_saved'] ? 'table-active' : '' }}">
                    <td class="align-middle d-none d-md-table-cell">
                        @if($invoiceProduct['is_saved'])
                            <input type="hidden" name="invoiceProducts[{{$index}}][product_id]" value="{{ $invoiceProduct['product_id'] }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-fill">
                                    <div class="font-weight-bold">{{ $invoiceProduct['product_name'] }}</div>
                                </div>
                            </div>
                        @else
                            <select wire:model.live="invoiceProducts.{{$index}}.product_id"
                                    id="invoiceProducts[{{$index}}][product_id]"
                                    class="form-control form-select @error('invoiceProducts.' . $index . '.product_id') is-invalid @enderror"
                            >
                                <option value="" disabled selected>-- Choose product --</option>
                                @foreach ($allProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('invoiceProducts.' . $index)
                                <em class="text-danger">
                                    {{ $message }}
                                </em>
                            @enderror
                        @endif
                    </td>
                    
                    <!-- Mobile view product column -->
                    <td class="align-middle d-md-none">
                        @if($invoiceProduct['is_saved'])
                            <input type="hidden" name="invoiceProducts[{{$index}}][product_id]" value="{{ $invoiceProduct['product_id'] }}">
                            <div class="d-flex align-items-center">
                                <div class="flex-fill">
                                    <div class="font-weight-bold">{{ $invoiceProduct['product_name'] }}</div>
                                </div>
                            </div>
                        @else
                            <select wire:model.live="invoiceProducts.{{$index}}.product_id"
                                    id="invoiceProducts[{{$index}}][product_id]"
                                    class="form-control form-select @error('invoiceProducts.' . $index . '.product_id') is-invalid @enderror"
                            >
                                <option value="" disabled selected>-- Product --</option>
                                @foreach ($allProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('invoiceProducts.' . $index)
                                <em class="text-danger">
                                    {{ $message }}
                                </em>
                            @enderror
                        @endif
                    </td>

                    <td class="align-middle text-center">
                        @if($invoiceProduct['is_saved'])
                            <span class="badge bg-primary-lt fs-6">{{ $invoiceProduct['quantity'] }}</span>
                            <input type="hidden"
                                   name="invoiceProducts[{{$index}}][quantity]"
                                   value="{{ $invoiceProduct['quantity'] }}"
                            >
                        @else
                            <input type="number"
                                   wire:model="invoiceProducts.{{$index}}.quantity"
                                   id="invoiceProducts[{{$index}}][quantity]"
                                   class="form-control text-center"
                                   min="1"
                                   value="1"
                            />
                        @endif
                    </td>

                    <td class="align-middle text-center d-none d-sm-table-cell">
                        @if($invoiceProduct['is_saved'])
                            <span class="text-success font-weight-bold">₱{{ number_format($invoiceProduct['product_price'], 2) }}</span>
                            <input type="hidden"
                                   name="invoiceProducts[{{$index}}][unitcost]"
                                   value="{{ $invoiceProduct['product_price'] }}"
                            >
                        @else
                            <span class="text-muted">--</span>
                        @endif
                    </td>

                    <td class="align-middle text-center">
                        @if($invoiceProduct['is_saved'])
                            <span class="text-success font-weight-bold fs-6">₱{{ number_format($invoiceProduct['product_price'] * $invoiceProduct['quantity'], 2) }}</span>
                        @else
                            <span class="text-muted">--</span>
                        @endif
                    </td>

                    <td class="align-middle text-center">
                        <div class="d-flex justify-content-center gap-2">
                            @if($invoiceProduct['is_saved'])
                                <button type="button" wire:click="editProduct({{$index}})" class="btn btn-icon btn-outline-warning" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                </button>
                            @elseif($invoiceProduct['product_id'])
                                <button type="button" wire:click="saveProduct({{$index}})" class="btn btn-icon btn-outline-success" title="Save">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                                </button>
                            @endif

                            <button type="button" wire:click="removeProduct({{$index}})" class="btn btn-icon btn-outline-danger" title="Remove">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-box-open fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">No products added yet</p>
                            <p class="empty-subtitle text-muted">
                                Click "Add Product" to start adding items to this purchase order
                            </p>
                        </div>
                    </td>
                </tr>
                @endforelse
                
                <tr class="table-light">
                    <td colspan="6" class="text-center p-3">
                        <button type="button" wire:click="addProduct" class="btn btn-outline-primary btn-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-1" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                            Add Product
                        </button>
                    </td>
                </tr>
            </tbody>
            
            <tfoot class="table-success">
                <tr>
                    <th colspan="5" class="align-middle text-end py-3">
                        <h4 class="mb-0">Subtotal</h4>
                    </th>
                    <th class="text-center py-3">
                        <h4 class="mb-0 text-success">₱{{ number_format($subtotal, 2) }}</h4>
                    </th>
                </tr>
                <tr>
                    <th colspan="5" class="align-middle text-end py-3">
                        <h4 class="mb-0">Total Amount</h4>
                    </th>
                    <th class="text-center py-3">
                        <h4 class="mb-0 text-primary">₱{{ number_format($total, 2) }}</h4>
                        <input type="hidden" name="total_amount" value="{{ $total }}">
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>
    
    @if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
</div>