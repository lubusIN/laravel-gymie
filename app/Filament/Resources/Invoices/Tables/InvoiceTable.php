<?php

namespace App\Filament\Resources\Invoices\Tables;

use Carbon\Carbon;
use App\Helpers\Helpers;
use App\Models\Invoice;
use App\Models\Subscription;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\Invoices\InvoiceResource;

class InvoiceTable
{
    /**
     * Configure the invoice table schema.
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
                    ->searchable(),
                TextColumn::make('number')
                    ->label('Invoice No.')
                    ->sortable(),
                TextColumn::make('subscription.member.name')
                    ->label('Subscription')
                    ->description(fn($record): string => $record->subscription->member->code),
                TextColumn::make('date')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('subscription_fee')
                    ->label('Fee')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state)),
                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state)),
                TextColumn::make('tax')
                    ->label('Tax')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state)),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state)),
                TextColumn::make('due_amount')
                    ->label('Due')
                    ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state)),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('date')
                    ->schema([
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
                Action::make('create_subscription')
                    ->label('New subscription')
                    ->url(fn() => route('filament.admin.resources.subscriptions.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Subscription::exists()),
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New invoice')
                    ->visible(fn() => Subscription::exists() && !Invoice::exists()),
            ])
            ->recordActions([
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('heading_status')
                            ->label('Manage Invoice')
                            ->disabled()
                            ->color('gray')
                            ->visible(fn($record) => in_array($record->status->value, ['issued', 'partial', 'overdue'])),
                        Action::make('mark_partially_paid')
                            ->label('Add Payment')
                            ->color('info')
                            ->icon('heroicon-s-banknotes')
                            ->schema([
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
                        Action::make('mark_paid')
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
                        Action::make('mark_refund')
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
                        Action::make('cancel_invoice')
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

                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        ViewAction::make()
                            ->url(fn($record) => InvoiceResource::getUrl('view', ['record' => $record])),
                        EditAction::make()
                            ->hidden(fn($record) => $record->status->value !== 'issued')
                            ->url(fn($record) => InvoiceResource::getUrl('edit', ['record' => $record])),
                        DeleteAction::make(),
                    ])->dropdown(false)
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
