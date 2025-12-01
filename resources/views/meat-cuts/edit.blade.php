@extends('layouts.butcher')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="page-title">
                <i class="fas fa-drumstick-bite me-2"></i>Edit Meat Cut
            </h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Edit: {{ $meatCut->name }}</h3>
                    <a href="{{ route('meat-cuts.index') }}" class="btn btn-secondary" style="color: white;">
                        <i class="fas fa-arrow-left me-2" style="color: white;"></i>Back to List
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('meat-cuts.update', $meatCut) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        @include('meat-cuts.form', ['meatCut' => $meatCut])
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary" style="color: white;">
                                <i class="fas fa-save me-2" style="color: white;"></i>Update Meat Cut
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
<style>
    .card-header {
        border-bottom: none;
    }
    .form-label {
        font-weight: 500;
    }
    .card {
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
@endpush 