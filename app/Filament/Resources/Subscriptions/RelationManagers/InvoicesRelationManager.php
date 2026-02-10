<?php

namespace App\Filament\Resources\Subscriptions\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Actions\CreateAction;
use App\Filament\Resources\Invoices\InvoiceResource;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static ?string $title = 'Manage Invoices';

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
        return InvoiceResource::form($schema);
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param Table $table
     * @return Table
     */
    public function table(Table $table): Table
    {
        return InvoiceResource::table($table)
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-s-plus')
                    ->modalHeading('New Invoice')
                    ->modalWidth('6xl')
                    ->closeModalByClickingAway(false)
                    ->createAnother(false)
            ]);;
    }
}
