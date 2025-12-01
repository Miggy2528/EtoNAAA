<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">{{ __('Suppliers') }}</h5>
                <p class="text-muted mb-0 small">Manage your supplier network</p>
            </div>

            <div class="card-actions">
                <x-action.create route="{{ route('suppliers.create') }}" />
            </div>
        </div>
    </div>

    <div class="card-body border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <div class="text-secondary me-3">
                    Show
                    <div class="mx-2 d-inline-block">
                        <select wire:model.live="perPage" class="form-select form-select-sm" aria-label="result per page">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                        </select>
                    </div>
                    entries
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="input-group input-group-sm" style="max-width: 300px;">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" wire:model.live="search" class="form-control border-start-0" placeholder="Search suppliers..." aria-label="Search suppliers">
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table wire:loading.remove class="table table-hover card-table table-vcenter text-nowrap datatable align-middle">
            <thead class="thead-light">
                <tr>
                    <th class="align-middle text-center w-1">
                        {{ __('No.') }}
                    </th>
                    <th scope="col" class="align-middle">
                        <a wire:click.prevent="sortBy('name')" href="#" role="button" class="text-decoration-none">
                            {{ __('Supplier') }}
                            @include('inclues._sort-icon', ['field' => 'name'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle">
                        <a wire:click.prevent="sortBy('email')" href="#" role="button" class="text-decoration-none">
                            {{ __('Contact') }}
                            @include('inclues._sort-icon', ['field' => 'email'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('type')" href="#" role="button" class="text-decoration-none">
                            {{ __('Type') }}
                            @include('inclues._sort-icon', ['field' => 'type'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        <a wire:click.prevent="sortBy('status')" href="#" role="button" class="text-decoration-none">
                            {{ __('Status') }}
                            @include('inclues._sort-icon', ['field' => 'status'])
                        </a>
                    </th>
                    <th scope="col" class="align-middle text-center">
                        {{ __('Actions') }}
                    </th>
                </tr>
            </thead>
            <tbody>
            @forelse ($suppliers as $supplier)
                <tr>
                    <td class="align-middle text-center">
                        {{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}
                    </td>
                    <td class="align-middle">
                        <div class="d-flex align-items-center">
                            @if($supplier->photo)
                                <img src="{{ Storage::url($supplier->photo) }}" alt="{{ $supplier->name }}" class="rounded-circle me-3" width="40" height="40">
                            @else
                                <div class="bg-light rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                            @endif
                            <div>
                                <div class="font-weight-medium">{{ $supplier->name }}</div>
                                <div class="text-muted small">{{ $supplier->shopname }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="align-middle">
                        <div class="text-muted small">{{ $supplier->email }}</div>
                        <div class="text-muted small">{{ $supplier->phone }}</div>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge bg-primary-subtle text-primary text-uppercase">
                            {{ $supplier->type }}
                        </span>
                    </td>
                    <td class="align-middle text-center">
                        <span class="badge {{ $supplier->status == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                            {{ $supplier->status }}
                        </span>
                    </td>
                    <td class="align-middle text-center">
                        <div class="d-flex gap-2 justify-content-center" role="group">
                            <x-button.show class="btn-sm" route="{{ route('suppliers.show', $supplier) }}" title="View Details"/>
                            <x-button.edit class="btn-sm" route="{{ route('suppliers.edit', $supplier) }}" title="Edit Supplier"/>
                            <x-button.delete class="btn-sm" route="{{ route('suppliers.destroy', $supplier) }}" title="Delete Supplier"/>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="align-middle text-center" colspan="6">
                        <div class="py-5 text-center">
                            <i class="fas fa-truck fa-2x text-muted mb-3"></i>
                            <h5>No results found</h5>
                            <p class="text-muted">Try adjusting your search or filter criteria</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer d-flex align-items-center justify-content-between">
        <div class="text-muted small">
            Showing <span>{{ $suppliers->firstItem() }}</span> to <span>{{ $suppliers->lastItem() }}</span> of <span>{{ $suppliers->total() }}</span> entries
        </div>

        <div class="pagination m-0 ms-auto">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>
