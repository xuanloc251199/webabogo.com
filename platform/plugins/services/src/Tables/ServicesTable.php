<?php

namespace Botble\Services\Tables;

use Botble\Services\Models\Services;
use Botble\Table\Abstracts\TableAbstract;
use Botble\Table\Actions\DeleteAction;
use Botble\Table\Actions\EditAction;
use Botble\Table\BulkActions\DeleteBulkAction;
use Botble\Table\Columns\Column;
use Botble\Table\Columns\CreatedAtColumn;
use Botble\Table\Columns\IdColumn;
use Botble\Table\Columns\ImageColumn;
use Botble\Table\Columns\StatusColumn;
use Botble\Table\Columns\NameColumn;
use Illuminate\Database\Eloquent\Builder;
use Botble\Table\HeaderActions\CreateHeaderAction;
use Botble\Table\BulkChanges\CreatedAtBulkChange;
use Botble\Table\BulkChanges\NameBulkChange;
use Botble\Table\BulkChanges\StatusBulkChange;

class ServicesTable extends TableAbstract
{
    public function setup(): void
    {
        $this
            ->model(Services::class)
            ->addHeaderAction(CreateHeaderAction::make()->route('services.create'))
            ->addActions([
                EditAction::make()->route('services.edit'),
                DeleteAction::make()->route('services.destroy'),
            ])
            ->addColumns([
                IdColumn::make(),
                ImageColumn::make(),
                NameColumn::make(),

                Column::make('price')
                    ->title(trans('plugins/ecommerce::products.price'))
                    ->alignStart(),
                CreatedAtColumn::make(),
                StatusColumn::make(),
            ])
            ->addBulkActions([
                DeleteBulkAction::make()->permission('services.destroy'),
            ])
            ->addBulkChanges([
                NameBulkChange::make(),
                StatusBulkChange::make(),
                CreatedAtBulkChange::make(),
            ])
            ->queryUsing(function (Builder $query) {
                $query->select([
                    'id',
                    'image',
                    'name',
                    'price',
                    'created_at',
                    'status',
                ]);
            });
    }
}
