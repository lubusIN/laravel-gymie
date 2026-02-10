<?php

namespace App\Filament\Resources\Enquiries\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Filament\Resources\FollowUps\FollowUpResource;
use App\Models\FollowUp;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class FollowUpsRelationManager extends RelationManager
{
    protected static string $relationship = 'followUps';

    protected static ?string $title = 'Follow Up Timeline';

    /**
     * Determine if the relation manager is read-only.
     *
     * @return bool Returns false, indicating the relation manager is not read-only.
     */
    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * Define the form schema for the resource.
     *
     * @param Schema $schema
     * @return Schema
     */
    public function form(Schema $schema): Schema
    {
        return FollowUpResource::form($schema);
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns(FollowUp::getTableColumns())
            ->headerActions([
                CreateAction::make('create')
                    ->icon('heroicon-m-plus')
                    ->visible(fn() => $this->getOwnerRecord()->followUps()->exists())
                    ->createAnother(false)
                    ->modalHeading('New follow up')
                    ->modalWidth('sm')
            ])
            ->emptyStateIcon('heroicon-o-arrow-path-rounded-square')
            ->emptyStateHeading('No Follow Ups')
            ->emptyStateDescription('Create follow-ups to get started.')
            ->emptyStateActions([
                CreateAction::make('create-follow-up')
                    ->icon('heroicon-o-plus')
                    ->label('New follow up')
                    ->createAnother(false)
                    ->modalHeading('New follow up')
                    ->modalWidth('sm'),
            ])
            ->filters(FollowUpResource::getTableFilters())
            ->recordActions(FollowUpResource::getTableActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
