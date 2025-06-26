<?php

namespace App\Filament\Resources\EnquiryResource\RelationManagers;

use App\Filament\Resources\FollowUpResource;
use App\Models\FollowUp;
use Filament\Forms\Form;
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
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public function form(Form $form): Form
    {
        return FollowUpResource::form($form);
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public function table(Table $table): Table
    {
        return $table
            ->columns(FollowUp::getTableColumns())
            ->headerActions([
                Tables\Actions\CreateAction::make('create')
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
                Tables\Actions\CreateAction::make('create-follow-up')
                    ->icon('heroicon-o-plus')
                    ->label('New follow up')
                    ->createAnother(false)
                    ->modalHeading('New follow up')
                    ->modalWidth('sm'),
            ])
            ->filters(FollowUpResource::getTableFilters())
            ->actions(FollowUpResource::getTableActions())
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
