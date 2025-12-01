@props([
    'label' => '',
    'name' => $name,
    'id' => '' ?? $name,
    'placeholder' => '',
    'data',
    'value',
    'class' => ''
])

<div class="mb-3">
    @if($label)
    <label for="{{ $id }}" class="form-label required fw-bold">
        {{ $label }}
    </label>
    @endif

    <select id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}" autocomplete="off"
            class="form-control form-select {{ $class }} @error($name) is-invalid @enderror"
    >
        <option value="" disabled selected>{{ $placeholder }}</option>

        @foreach($data as $option)
            <option value="{{ $option->id }}" @selected(old($name, $value ?? null) == $option->id)>
                {{ $option->name }}
            </option>
        @endforeach
    </select>

    @error($name)
    <div class="invalid-feedback d-block">
        {{ $message }}
    </div>
    @enderror
</div>

@pushonce('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-dropdown {
            z-index: 1055 !important;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
            border: 1px solid #dee2e6 !important;
        }
        
        .ts-wrapper.single .ts-control {
            border-color: #ced4da !important;
            border-radius: 0.375rem !important;
        }
        
        .ts-wrapper.single .ts-control:focus {
            border-color: #86b7fe !important;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25) !important;
        }
        
        .ts-wrapper.single .ts-control, 
        .ts-wrapper.single .ts-control input {
            cursor: pointer;
            min-height: calc(1.5em + 0.75rem + 2px) !important;
        }
        
        .ts-dropdown .optgroup-header {
            font-weight: bold;
            background: #f8f9fa;
            padding: 5px 10px;
        }
        
        /* Ensure dropdown is positioned correctly */
        .ts-dropdown.dropdown-input {
            position: absolute !important;
        }
        
        /* Large select styling */
        .form-control-lg.ts-wrapper.single .ts-control {
            min-height: calc(1.5em + 1rem + 2px) !important;
            padding: 0.5rem 1rem !important;
        }
    </style>
@endpushonce

@pushonce('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new TomSelect("#{{ $id }}",{
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                placeholder: "{{ $placeholder }}",
                plugins: {
                    dropdown_input: {},
                    clear_button: {
                        title: 'Remove selection',
                    }
                },
                dropdownParent: 'body'
            });
        });
    </script>
@endpushonce