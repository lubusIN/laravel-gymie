<?php

namespace App\Filament\Resources\Expenses\Schemas;

use App\Helpers\Helpers;
use App\Models\Expense;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class ExpenseInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Fieldset::make('Expense Details')
                    ->label(function (Expense $record): HtmlString {
                        $status = $record->status;
                        $html = Blade::render(
                            '<x-filament::badge class="inline-flex ml-2" :color="$color">
                                {{ $label }}
                            </x-filament::badge>',
                            [
                                'color' => $status->getColor(),
                                'label' => $status->getLabel(),
                            ]
                        );
                        return new HtmlString($html);
                    })
                    ->schema([
                        TextEntry::make('name')
                            ->label('Expense'),
                        TextEntry::make('category')
                            ->label('Category')
                            ->formatStateUsing(fn (?string $state): string => Helpers::getExpenseCategoryLabel($state) ?? 'N/A'),
                        TextEntry::make('vendor')
                            ->label('Vendor')
                            ->placeholder('N/A'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(Helpers::getCurrencyCode()),
                        TextEntry::make('date')
                            ->label('Date')
                            ->date(),
                        TextEntry::make('due_date')
                            ->label('Due Date')
                            ->date()
                            ->placeholder('N/A'),
                        TextEntry::make('paid_at')
                            ->label('Paid at')
                            ->dateTime()
                            ->placeholder('N/A'),
                        TextEntry::make('notes')
                            ->label('Notes')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
