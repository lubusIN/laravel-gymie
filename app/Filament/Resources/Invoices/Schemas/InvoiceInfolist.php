<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Helpers\Helpers;
use App\Models\Invoice;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class InvoiceInfolist
{
    /**
     * Configure the invoice "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make(4)
                    ->schema([
                        Section::make()
                            ->heading(function (Invoice $record): HtmlString {
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
                                return new HtmlString('Details ' . $html);
                            })
                            ->schema([
                                TextEntry::make('number')->label('Invoice No.'),
                                TextEntry::make('subscription.member')
                                    ->label('Subscription')
                                    ->weight(FontWeight::Bold)
                                    ->color('success')
                                    ->formatStateUsing(fn($record): string => "{$record->subscription->member->code} – {$record->subscription->member->name}")
                                    ->url(fn($record): string => route('filament.admin.resources.subscriptions.view', $record->subscription_id)),
                                TextEntry::make('date')->date(),
                                TextEntry::make('due_date')
                                    ->label('Due Date')
                                    ->date(),
                                TextEntry::make('payment_method')
                                    ->label('Payment Method'),
                                TextEntry::make('discount_note')
                                    ->label('Discount Note')
                                    ->placeholder('N/A'),
                            ])
                            ->columns(3)
                            ->columnSpan(3),

                        Section::make('Summary')
                            ->schema([
                                Flex::make([
                                    TextEntry::make('fee_label')
                                        ->label('Fee:'),
                                    TextEntry::make('subscription_fee')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->subscription_fee)),
                                ]),
                                Flex::make([
                                    TextEntry::make('tax_label')
                                        ->label('Tax (' . Helpers::getTaxRate() . '%): '),
                                    TextEntry::make('tax')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->tax)),
                                ])->hidden(fn($record) => empty($record->tax)),
                                Flex::make([
                                    TextEntry::make('discount_label')
                                        ->label(fn(Invoice $record) => $record->discount ? 'Discount (' . $record->discount . ')%:' : 'Discount'),
                                    TextEntry::make('discount_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->discount_amount)),
                                ])->hidden(fn($record) => empty($record->discount_amount)),
                                Flex::make([
                                    TextEntry::make('total_label')
                                        ->label('Total:'),
                                    TextEntry::make('total_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->total_amount)),
                                ]),
                                Flex::make([
                                    TextEntry::make('paid_label')
                                        ->label('Paid:'),
                                    TextEntry::make('paid_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->paid_amount)),
                                ])->hidden(fn($record) => empty($record->paid_amount)),
                                Flex::make([
                                    TextEntry::make('due_label')
                                        ->label('Due:'),
                                    TextEntry::make('due_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->due_amount)),
                                ])->hidden(fn($record) => empty($record->due_amount)),

                            ])
                            ->columns(1)
                            ->columnSpan(1),
                    ]),
            ]);
    }
}
