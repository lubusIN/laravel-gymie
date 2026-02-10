<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;

class ServiceTable
{
    /**
     * Configure the service table schema.
     *
     * @param Table $table
     * @return Table
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name')
                    ->sortable(),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description'),
                TextColumn::make('created_at')
                    ->searchable()
                    ->date('d-m-Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateIcon('heroicon-o-cog-8-tooth')
            ->emptyStateHeading('No Services')
            ->emptyStateDescription('Create a service to get started.')
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New service')
                    ->modalHeading('New service')
                    ->modalWidth('sm')
                    ->createAnother(false)
                    ->hidden(fn() => Service::exists()),
            ])
            ->recordActions([
                ViewAction::make()
                    ->modalCancelAction(false)
                    ->modalWidth('sm'),
                EditAction::make()->modalWidth('sm'),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
