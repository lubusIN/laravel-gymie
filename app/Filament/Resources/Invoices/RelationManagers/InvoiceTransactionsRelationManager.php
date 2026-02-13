<?php

namespace App\Filament\Resources\Invoices\RelationManagers;

use App\Helpers\Helpers;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class InvoiceTransactionsRelationManager extends RelationManager
{
    protected static string $relationship = 'transactions';

    protected static ?string $title = 'Payment History';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),
                TextColumn::make('amount')
                    ->label('Amount')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->placeholder('-'),
                TextColumn::make('note')
                    ->label('Note')
                    ->wrap()
                    ->placeholder('-'),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
