<div class="row">
    <div class="col-md-6">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Basic Information</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $meatCut->name ?? '') }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" 
                              id="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="3" 
                              placeholder="Describe the meat cut...">{{ old('description', $meatCut->description ?? '') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label for="image" class="form-label">Image</label>
                    <input type="file" 
                           name="image" 
                           id="image" 
                           class="form-control @error('image') is-invalid @enderror" 
                           accept="image/*">
                    @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @if(isset($meatCut) && $meatCut->image_path)
                        <div class="mt-2">
                            <img src="{{ Storage::url($meatCut->image_path) }}" 
                                 alt="{{ $meatCut->name }}" 
                                 class="img-thumbnail" 
                                 style="max-width: 200px;">
                            <div class="form-text">Current image</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Classification -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Classification</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="meat_type" class="form-label">Meat Type <span class="text-danger">*</span></label>
                    <select name="meat_type" 
                            id="meat_type" 
                            class="form-select @error('meat_type') is-invalid @enderror" 
                            required>
                        <option value="">Select Meat Type</option>
                        @foreach(['Beef', 'Pork', 'Chicken', 'Lamb', 'Goat'] as $type)
                            <option value="{{ strtolower($type) }}" 
                                    {{ old('meat_type', $meatCut->meat_type ?? '') == strtolower($type) ? 'selected' : '' }}>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                    @error('meat_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="meat_subtype" class="form-label">Meat Subtype</label>
                            <input type="text" 
                                   name="meat_subtype" 
                                   id="meat_subtype" 
                                   class="form-control @error('meat_subtype') is-invalid @enderror" 
                                   value="{{ old('meat_subtype', $meatCut->meat_subtype ?? '') }}" 
                                   placeholder="e.g., steak, chop, breast">
                            @error('meat_subtype')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="quality_grade" class="form-label">Quality Grade <span class="text-danger">*</span></label>
                            <select name="quality_grade" 
                                    id="quality_grade" 
                                    class="form-select @error('quality_grade') is-invalid @enderror" 
                                    required>
                                <option value="">Select Quality Grade</option>
                                @foreach(['Prime', 'Choice', 'Select', 'Standard'] as $grade)
                                    <option value="{{ strtolower($grade) }}" 
                                            {{ old('quality_grade', $meatCut->quality_grade ?? '') == strtolower($grade) ? 'selected' : '' }}>
                                        {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                            @error('quality_grade')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="quality" class="form-label">Quality</label>
                            <input type="text" 
                                   name="quality" 
                                   id="quality" 
                                   class="form-control @error('quality') is-invalid @enderror" 
                                   value="{{ old('quality', $meatCut->quality ?? '') }}" 
                                   placeholder="e.g., premium, choice, standard">
                            @error('quality')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="preparation_type" class="form-label">Preparation Type</label>
                            <input type="text" 
                                   name="preparation_type" 
                                   id="preparation_type" 
                                   class="form-control @error('preparation_type') is-invalid @enderror" 
                                   value="{{ old('preparation_type', $meatCut->preparation_type ?? '') }}" 
                                   placeholder="e.g., grill, roast, smoke">
                            @error('preparation_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="preparation_style" class="form-label">Preparation Style</label>
                    <input type="text" 
                           name="preparation_style" 
                           id="preparation_style" 
                           class="form-control @error('preparation_style') is-invalid @enderror" 
                           value="{{ old('preparation_style', $meatCut->preparation_style ?? '') }}" 
                           placeholder="e.g., dry aged, marinated, glazed">
                    @error('preparation_style')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <!-- Pricing & Inventory -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Pricing & Inventory</h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label for="default_price_per_kg" class="form-label">Price per Kilogram (₱) <span class="text-danger">*</span></label>
                    <input type="number" 
                           name="default_price_per_kg" 
                           id="default_price_per_kg" 
                           class="form-control @error('default_price_per_kg') is-invalid @enderror" 
                           value="{{ old('default_price_per_kg', $meatCut->default_price_per_kg ?? '') }}" 
                           step="0.01" 
                           min="0" 
                           required>
                    @error('default_price_per_kg')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="minimum_stock_level" class="form-label">Minimum Stock Level <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="minimum_stock_level" 
                                   id="minimum_stock_level" 
                                   class="form-control @error('minimum_stock_level') is-invalid @enderror" 
                                   value="{{ old('minimum_stock_level', $meatCut->minimum_stock_level ?? '10') }}" 
                                   min="0" 
                                   required>
                            @error('minimum_stock_level')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    @if(isset($meatCut))
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="current_quantity" class="form-label">Current Quantity</label>
                            <input type="number" 
                                   id="current_quantity" 
                                   class="form-control" 
                                   value="{{ $meatCut->quantity ?? 0 }}" 
                                   disabled>
                        </div>
                    </div>
                    @endif
                </div>

                @if(isset($meatCut))
                <div class="form-group mb-3">
                    <div class="form-check form-switch">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="is_available" 
                               name="is_available" 
                               value="1" 
                               {{ old('is_available', $meatCut->is_available) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_available">Available for Sale</label>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_by_product" 
                                       name="is_by_product" 
                                       value="1" 
                                       {{ old('is_by_product', $meatCut->is_by_product ?? $prefillData['is_by_product'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_by_product">By-Product</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" 
                                       class="form-check-input" 
                                       id="is_processing_meat" 
                                       name="is_processing_meat" 
                                       value="1" 
                                       {{ old('is_processing_meat', $meatCut->is_processing_meat ?? $prefillData['is_processing_meat'] ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_processing_meat">Processing Meat</label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Preview -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Preview</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-1"></i>
                    This section will show how the meat cut will appear in the system.
                </div>
                <div class="preview-container">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="preview-image-placeholder bg-light rounded mb-3 d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                <i class="fas fa-image text-muted fs-1"></i>
                            </div>
                            <h5 class="card-title mb-2" id="preview-name">Meat Cut Name</h5>
                            <div class="mb-2" id="preview-classifications">
                                <span class="badge bg-secondary" id="preview-meat-type">Meat Type</span>
                                <span class="badge bg-info text-dark" id="preview-quality-grade">Quality Grade</span>
                            </div>
                            <div class="mb-3" id="preview-detailed-classifications">
                                <small class="text-muted d-block mb-1">Classifications</small>
                                <span class="badge bg-light text-dark me-1" id="preview-meat-subtype">Subtype</span>
                                <span class="badge bg-light text-dark me-1" id="preview-quality">Quality</span>
                                <span class="badge bg-light text-dark me-1" id="preview-preparation-type">Prep Type</span>
                                <span class="badge bg-light text-dark" id="preview-preparation-style">Prep Style</span>
                            </div>
                            <p class="mb-1" id="preview-price">Price/kg: ₱<span>0.00</span></p>
                            <p class="mb-1">Quantity: <span id="preview-quantity">0</span></p>
                            <p class="mb-1">Status: <span class="badge bg-success" id="preview-status">Available</span></p>
                            <p class="mb-0">Min. Stock: <span id="preview-min-stock">10</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('page-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get form elements
        const nameInput = document.getElementById('name');
        const meatTypeSelect = document.getElementById('meat_type');
        const qualityGradeSelect = document.getElementById('quality_grade');
        const meatSubtypeInput = document.getElementById('meat_subtype');
        const qualityInput = document.getElementById('quality');
        const preparationTypeInput = document.getElementById('preparation_type');
        const preparationStyleInput = document.getElementById('preparation_style');
        const priceInput = document.getElementById('default_price_per_kg');
        const minStockInput = document.getElementById('minimum_stock_level');
        const isAvailableCheckbox = document.getElementById('is_available');
        const isByProductCheckbox = document.getElementById('is_by_product');
        const isProcessingMeatCheckbox = document.getElementById('is_processing_meat');
        
        // Get preview elements
        const previewName = document.getElementById('preview-name');
        const previewMeatType = document.getElementById('preview-meat-type');
        const previewQualityGrade = document.getElementById('preview-quality-grade');
        const previewMeatSubtype = document.getElementById('preview-meat-subtype');
        const previewQuality = document.getElementById('preview-quality');
        const previewPreparationType = document.getElementById('preview-preparation-type');
        const previewPreparationStyle = document.getElementById('preview-preparation-style');
        const previewPrice = document.querySelector('#preview-price span');
        const previewQuantity = document.getElementById('preview-quantity');
        const previewStatus = document.getElementById('preview-status');
        const previewMinStock = document.getElementById('preview-min-stock');
        
        // Function to update preview
        function updatePreview() {
            // Update name
            previewName.textContent = nameInput.value || 'Meat Cut Name';
            
            // Update meat type
            if (meatTypeSelect.value) {
                previewMeatType.textContent = meatTypeSelect.options[meatTypeSelect.selectedIndex].text;
                previewMeatType.classList.remove('bg-secondary');
                previewMeatType.classList.add('bg-info');
            } else {
                previewMeatType.textContent = 'Meat Type';
                previewMeatType.classList.remove('bg-info');
                previewMeatType.classList.add('bg-secondary');
            }
            
            // Update quality grade
            if (qualityGradeSelect.value) {
                previewQualityGrade.textContent = qualityGradeSelect.options[qualityGradeSelect.selectedIndex].text;
                previewQualityGrade.classList.remove('bg-secondary');
                previewQualityGrade.classList.add('bg-warning', 'text-dark');
            } else {
                previewQualityGrade.textContent = 'Quality Grade';
                previewQualityGrade.classList.remove('bg-warning', 'text-dark');
                previewQualityGrade.classList.add('bg-secondary');
            }
            
            // Update meat subtype
            previewMeatSubtype.textContent = meatSubtypeInput.value || 'Subtype';
            
            // Update quality
            previewQuality.textContent = qualityInput.value || 'Quality';
            
            // Update preparation type
            previewPreparationType.textContent = preparationTypeInput.value || 'Prep Type';
            
            // Update preparation style
            previewPreparationStyle.textContent = preparationStyleInput.value || 'Prep Style';
            
            // Update price
            previewPrice.textContent = parseFloat(priceInput.value) || '0.00';
            
            // Update minimum stock
            previewMinStock.textContent = minStockInput.value || '10';
            
            // Update status
            if (isByProductCheckbox && isByProductCheckbox.checked) {
                previewStatus.textContent = 'By-Product';
                previewStatus.className = 'badge bg-warning text-dark';
            } else if (isProcessingMeatCheckbox && isProcessingMeatCheckbox.checked) {
                previewStatus.textContent = 'Processing';
                previewStatus.className = 'badge bg-primary';
            } else if (isAvailableCheckbox) {
                previewStatus.textContent = isAvailableCheckbox.checked ? 'Available' : 'Not Available';
                previewStatus.className = isAvailableCheckbox.checked ? 'badge bg-success' : 'badge bg-danger';
            }
        }
        
        // Add event listeners
        if (nameInput) nameInput.addEventListener('input', updatePreview);
        if (meatTypeSelect) meatTypeSelect.addEventListener('change', updatePreview);
        if (qualityGradeSelect) qualityGradeSelect.addEventListener('change', updatePreview);
        if (meatSubtypeInput) meatSubtypeInput.addEventListener('input', updatePreview);
        if (qualityInput) qualityInput.addEventListener('input', updatePreview);
        if (preparationTypeInput) preparationTypeInput.addEventListener('input', updatePreview);
        if (preparationStyleInput) preparationStyleInput.addEventListener('input', updatePreview);
        if (priceInput) priceInput.addEventListener('input', updatePreview);
        if (minStockInput) minStockInput.addEventListener('input', updatePreview);
        if (isAvailableCheckbox) isAvailableCheckbox.addEventListener('change', updatePreview);
        if (isByProductCheckbox) isByProductCheckbox.addEventListener('change', updatePreview);
        if (isProcessingMeatCheckbox) isProcessingMeatCheckbox.addEventListener('change', updatePreview);
        
        // Initialize preview with existing values (for edit form) or pre-filled data (for create form)
        updatePreview();
    });
</script>
@endpush