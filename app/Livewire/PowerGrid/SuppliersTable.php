<?php

namespace App\Livewire\PowerGrid;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridColumns;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SuppliersTable extends PowerGridComponent
{
    public int $perPage = 10;
    public array $perPageValues = [10, 25, 50, 100];

    public function setUp(): array
    {
        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            Header::make()
                ->showSearchInput()
                ->showToggleColumns(),

            Footer::make()
                ->showPerPage($this->perPage, $this->perPageValues)
                ->showRecordCount('min')
        ];
    }

    public function datasource(): Builder
    {
        return Supplier::query()
            ->withCount('procurements');
    }

    public function addColumns(): PowerGridColumns
    {
        return PowerGrid::columns()
            ->addColumn('id')
            ->addColumn('name')
            ->addColumn('shopname')
            ->addColumn('email')
            ->addColumn('phone')
            ->addColumn('type')
            ->addColumn('status')
            ->addColumn('procurements_count')
            ->addColumn('created_at')
            ->addColumn('created_at_formatted', fn (Supplier $model) => Carbon::parse($model->created_at)->format('d/m/Y'));
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
                ->searchable()
                ->sortable(),

            Column::make('Supplier', 'name')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
                ->searchable()
                ->sortable()
                ->format(
                    fn (Supplier $model) => "<div class='d-flex align-items-center'>
                        <div class='bg-light rounded-circle me-3 d-flex align-items-center justify-content-center' style='width: 40px; height: 40px;'>
                            <i class='fas fa-user text-muted'></i>
                        </div>
                        <div>
                            <div class='font-weight-medium'>{$model->name}</div>
                            <div class='text-muted small'>{$model->shopname}</div>
                        </div>
                    </div>"
                ),

            Column::make('Contact', 'email')
                ->headerAttribute('text-left')
                ->bodyAttribute('text-left')
                ->searchable()
                ->sortable()
                ->format(
                    fn (Supplier $model) => "<div class='text-muted small'>{$model->email}</div>
                        <div class='text-muted small'>{$model->phone}</div>"
                ),

            Column::make('Type', 'type')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable()
                ->sortable()
                ->format(
                    fn (Supplier $model) => "<span class='badge bg-primary-subtle text-primary text-uppercase'>
                        {$model->type}
                    </span>"
                ),

            Column::make('Status', 'status')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable()
                ->sortable()
                ->format(
                    fn (Supplier $model) => "<span class='badge ' . ($model->status == 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger') . ' text-uppercase'>
                        {$model->status}
                    </span>"
                ),

            Column::make('Procurements', 'procurements_count')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable()
                ->sortable()
                ->format(
                    fn (Supplier $model) => "<div class='d-flex align-items-center justify-content-center'>
                        <div class='me-2'>
                            <i class='fas fa-boxes text-primary'></i>
                        </div>
                        <div>
                            <div class='fw-bold'>{$model->procurements_count}</div>
                        </div>
                    </div>"
                ),

            Column::make('Created at', 'created_at')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->hidden(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->headerAttribute('text-center')
                ->bodyAttribute('text-center')
                ->searchable(),

            Column::action('Actions')
                ->headerAttribute('text-center', styleAttr: 'width: 150px;')
                ->bodyAttribute('text-center')
        ];
    }

    public function filters(): array
    {
        return [
            //
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions(\App\Models\Supplier $row): array
    {
        return [
            Button::make('show')
                ->slot('<i class="fas fa-eye"></i>')
                ->class('btn btn-outline-primary btn-sm')
                ->tooltip('View Details')
                ->route('suppliers.show', ['supplier' => $row])
                ->method('get'),

            Button::make('edit')
                ->slot('<i class="fas fa-edit"></i>')
                ->class('btn btn-outline-warning btn-sm')
                ->tooltip('Edit Supplier')
                ->route('suppliers.edit', ['supplier' => $row])
                ->method('get'),

            Button::add('delete')
                ->slot('<i class="fas fa-trash"></i>')
                ->class('btn btn-outline-danger btn-sm')
                ->tooltip('Delete Supplier')
                ->route('suppliers.destroy', ['supplier' => $row])
                ->method('delete'),
        ];
    }

    /*
    public function actionRules(\App\Models\Supplier $row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
