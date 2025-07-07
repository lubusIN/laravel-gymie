<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Helpers\Helpers;
use App\Models\Invoice;
use App\Models\Subscription;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Invoice::getForm());
    }

    /**
     * Get the Filament table columns for the invoice list view.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(Invoice::getTableColumns())
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_to'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->emptyStateIcon(
                !Subscription::exists()
                    ? 'heroicon-o-ticket'
                    : 'heroicon-o-document-text'
            )
            ->emptyStateHeading(function ($livewire): string {
                // If no subscription exist
                if (!Subscription::exists()) {
                    return 'No Subscriptions';
                }

                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'issued'  => 'No Issued Invoices',
                    'partial' => 'No Partial Invoices',
                    'overdue' => 'No Overdue Invoices',
                    'paid'    => 'No Paid Invoices',
                    'refund'  => 'No Refund Invoices',
                    'cancelled' => 'No Cancelled Invoices',
                ][$tab] ?? 'No Invoices';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Invoices in Date Range';
                }

                return Subscription::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                // If no subscriptions exist
                if (!Subscription::exists()) {
                    return 'Create a subscription to get started.';
                }

                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'issued'    => 'There are no invoices marked as issued.',
                    'partial'   => 'There are no invoices marked as partially paid.',
                    'overdue'   => 'There are no invoices marked as overdue.',
                    'paid'      => 'There are no invoices marked as paid.',
                    'refund'    => 'There are no invoices marked as refund.',
                    'cancelled' => 'There are no invoices marked as cancelled.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a invoice to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no invoices created between {$from} and {$to}.";
                }

                if (!Invoice::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a invoice to get started.';
                }

                return "We found no {$tab} invoices between {$from} and {$to}.";
            })
            ->emptyStateActions([
                Tables\Actions\Action::make('create_subscription')
                    ->label('New subscription')
                    ->url(fn() => route('filament.admin.resources.subscriptions.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Subscription::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New invoice')
                    ->visible(fn() => Subscription::exists() && !Invoice::exists()),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_status')
                            ->label('Manage Invoice')
                            ->disabled()
                            ->color('gray')
                            ->visible(fn($record) => in_array($record->status->value, ['issued', 'partial', 'overdue'])),
                        Tables\Actions\Action::make('mark_partially_paid')
                            ->label('Add Payment')
                            ->color('info')
                            ->icon('heroicon-s-banknotes')
                            ->form([
                                TextInput::make('amount')
                                    ->label("Amount (" . Helpers::getCurrencyCode() . ")")
                                    ->required()
                                    ->numeric()
                                    ->reactive()
                                    ->placeholder('Enter amount')
                                    ->validationAttribute('amount')
                                    ->helperText(fn(Invoice $record) => "Due Amount: " . ($record->total_amount - $record->paid_amount))
                                    ->maxValue(fn(Invoice $record) => $record->total_amount - $record->paid_amount)
                                    ->minValue(0)
                                    ->afterStateUpdated(function ($livewire, TextInput $component) {
                                        $livewire->validateOnly($component->getStatePath());
                                    }),
                            ])
                            ->action(function (Invoice $record, array $data) {
                                $entered       = $data['amount'];
                                $newPaid       = $record->paid_amount + $entered;
                                $newDue        = max($record->total_amount - $newPaid, 0.0);
                                $newStatus     = $newPaid >= $record->total_amount ? 'paid' : 'partial';
                                $formatedValue = Helpers::formatCurrency($newPaid, Helpers::getCurrencyCode());

                                $record->update([
                                    'paid_amount' => $newPaid,
                                    'due_amount'  => $newDue,
                                    'status'      => $newStatus,
                                ]);

                                if ($record->status->value == 'paid') {
                                    Notification::make()
                                        ->title('Invoice Paid')
                                        ->success()
                                        ->body("Invoice #{$record->number} has been fully Paid with amount {$formatedValue}.")
                                        ->send();
                                }

                                if ($record->status->value == 'partial') {
                                    Notification::make()
                                        ->title('Invoice Partially Paid')
                                        ->warning()
                                        ->body("Invoice #{$record->number} has been marked as Partial with amount {$formatedValue}.")
                                        ->send();
                                }
                            })
                            ->visible(fn($record) => in_array($record->status, ['issued', 'overdue', 'partial'])),
                        Tables\Actions\Action::make('mark_paid')
                            ->label('Mark as Paid')
                            ->color('success')
                            ->icon('heroicon-s-check-circle')
                            ->requiresConfirmation()
                            ->action(function (Invoice $record) {
                                $record->update([
                                    'status' => 'paid',
                                ]);
                                Notification::make()
                                    ->title('Invoice Paid')
                                    ->success()
                                    ->body("Invoice #{$record->number} has been fully paid with amount ₹{$record->paid_amount}.")
                                    ->send();
                            })
                            ->visible(fn($record) => in_array($record->status->value, ['issued', 'partial', 'overdue'])),
                        Tables\Actions\Action::make('mark_refund')
                            ->label('Refund')
                            ->color('warning')
                            ->icon('heroicon-s-arrow-path')
                            ->action(fn(Invoice $record) => tap($record, function ($record) {
                                $record->update(['status' => 'refund']);
                                Notification::make()
                                    ->title('Invoice Updated')
                                    ->warning()
                                    ->body("Invoice #{$record->number} has been refunded.")
                                    ->send();
                            }))
                            ->visible(fn($record) => in_array($record->status->value, ['paid', 'partial', 'overdue'])),
                        Tables\Actions\Action::make('cancel_invoice')
                            ->label('Cancel')
                            ->color('danger')
                            ->icon('heroicon-s-x-circle')
                            ->action(fn(Invoice $record) => tap($record, function ($record) {
                                $record->update(['status' => 'cancelled']);
                                Notification::make()
                                    ->title('Invoice Cancelled')
                                    ->danger()
                                    ->body("Invoice #{$record->number} has been cancelled.")
                                    ->send();
                            }))
                            ->visible(fn($record) => !in_array($record->status->value, ['cancelled', 'paid', 'refund'])),
                    ])
                        ->dropdown(false),

                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\ViewAction::make()
                            ->url(fn($record) => InvoiceResource::getUrl('view', ['record' => $record])),
                        Tables\Actions\EditAction::make()
                            ->hidden(fn($record) => $record->status->value !== 'issued')
                            ->url(fn($record) => InvoiceResource::getUrl('edit', ['record' => $record])),
                        Tables\Actions\DeleteAction::make(),
                    ])->dropdown(false)
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
                                Split::make([
                                    TextEntry::make('')
                                        ->label('Fee:'),
                                    TextEntry::make('subscription_fee')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->subscription_fee)),
                                ]),
                                Split::make([
                                    TextEntry::make('')
                                        ->label('Tax (' . Helpers::getTaxRate() . '%): '),
                                    TextEntry::make('tax')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->tax)),
                                ])->hidden(fn($record) => empty($record->tax)),
                                Split::make([
                                    TextEntry::make('')
                                        ->label(fn(Invoice $record) => $record->discount ? 'Discount (' . $record->discount . ')%:' : 'Discount'),
                                    TextEntry::make('discount_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->discount_amount)),
                                ])->hidden(fn($record) => empty($record->discount_amount)),
                                Split::make([
                                    TextEntry::make('')
                                        ->label('Total:'),
                                    TextEntry::make('total_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->total_amount)),
                                ]),
                                Split::make([
                                    TextEntry::make('')
                                        ->label('Paid:'),
                                    TextEntry::make('paid_amount')
                                        ->hiddenLabel()
                                        ->formatStateUsing(fn(Invoice $record) => Helpers::formatCurrency($record->paid_amount)),
                                ])->hidden(fn($record) => empty($record->paid_amount)),
                                Split::make([
                                    TextEntry::make('')
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
