@extends('layouts.butcher')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <x-alert/>

        <div class="row row-cards">

            <form action="{{ route('purchases.store') }}" method="POST">
                @csrf
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title">
                                        {{ __('Create Purchase') }}
                                    </h3>
                                </div>

                                <div class="card-actions btn-actions">
                                    <div class="dropdown">
                                        <a href="#" class="btn-action dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><!-- Download SVG icon from http://tabler-icons.io/i/dots-vertical -->
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M12 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path><path d="M12 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path></svg>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                            {{--- ---}}
                                        </div>
                                    </div>

                                    {{--- {{ URL::previous() }} ---}}
                                    <a href="{{ url()->previous() ?: route('purchases.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-4">
                                        <label for="date" class="form-label required">
                                            {{ __('Purchase Date') }}
                                        </label>

                                        <input name="date" id="date" type="date"
                                               class="form-control example-date-input

                                               @error('date') is-invalid @enderror"
                                               value="{{ old('date') ?? now()->format('Y-m-d') }}"
                                               required
                                        >

                                        @error('date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>


                                    <x-tom-select
                                        label="Suppliers"
                                        id="supplier_id"
                                        name="supplier_id"
                                        placeholder="Select Supplier"
                                        :data="$suppliers"
                                    />

                                </div>

                                @livewire('purchase-form')
                            </div>

                            <div class="card-footer text-end">
                                {{--- onclick="return confirm('Are you sure you want to purchase?')" ---}}
                                {{--- @disabled($errors->isNotEmpty()) ---}}
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Purchase') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
