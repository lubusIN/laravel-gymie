<?php

namespace App\Models;

use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'number',
        'subscription_id',
        'date',
        'due_date',
        'payment_method',
        'status',
        'tax',
        'discount',
        'discount_amount',
        'discount_note',
        'paid_amount',
        'total_amount',
        'due_amount',
        'subscription_fee',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date'        => 'date',
        'due_date'    => 'date',
    ];

    /**
     * The member who owns this invoice.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The subscription this invoice is for.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Boot the model and handle invoice calculations on saving.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($invoice) {
            if (!$invoice->number) {
                $invoice->number = Helpers::generateLastNumber('invoice', Invoice::class, $invoice->date);
            }
            Helpers::updateLastNumber('invoice', $invoice->number, $invoice->date);

            $fee = $invoice->subscription_fee ?? 0;
            $taxRate = Helpers::getTaxRate() ?: 0;
            $discountAmount = $invoice->discount_amount ?? 0;
            $itemsTotal = max($fee - $discountAmount, 0);
            $itemsTotal = round($itemsTotal, 2);
            $totalTax = round(($fee * $taxRate) / 100, 2);
            $paid = $invoice->paid_amount ?? 0;
            $totalAmount = $itemsTotal + $totalTax;

            // Handle paid amount based on status
            if ($invoice->status === 'paid') {
                $paid = $totalAmount;
            } elseif (in_array($invoice->status, ['cancelled', 'refund'])) {
                $paid = 0;
            } else {
                $paid = $invoice->paid_amount ?? 0;
                // Ensure paid amount doesn't exceed total amount
                if ($paid > $totalAmount) {
                    $paid = $totalAmount;
                }
            }

            $dueAmount = $totalAmount - $paid;

            // Update status if not explicitly set based on payment conditions
            if (!in_array($invoice->status, ['paid', 'cancelled', 'refund'])) {
                if ($dueAmount <= 0) {
                    $status = 'paid';
                } elseif ($paid > 0) {
                    $status = 'partial';
                } else {
                    $status = $invoice->status ?? 'issued';
                }
            } else {
                $status = $invoice->status;
            }

            $invoice->subscription_fee = $itemsTotal;
            $invoice->total_amount = $totalAmount;
            $invoice->paid_amount = $paid;
            $invoice->tax = $totalTax;
            $invoice->due_amount = $dueAmount;
            $invoice->status = $status;
        });
    }

    /**
     * Get the Filament form schema for the invoice.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Section::make('Details')
                ->schema([
                    TextInput::make('number')
                        ->label('Invoice No.')
                        ->required()
                        ->readOnly()
                        ->unique('invoices', 'number')
                        ->default(fn(Get $get) => Helpers::generateLastNumber(
                            'invoice',
                            Invoice::class,
                            $get('date')
                        )),
                    Select::make('subscription_id')
                        ->label('Subscription')
                        ->reactive()
                        ->relationship('subscription', 'id')
                        ->getOptionLabelFromRecordUsing(fn(Subscription $record): string => "{$record->member->code} - {$record->member->name}")
                        ->afterStateUpdated(
                            function (Get $get, Set $set) {
                                $sub = $get('subscription_id')
                                    ? Subscription::with('plan')->find($get('subscription_id'))
                                    : null;

                                if ($sub) {
                                    $fee     = $sub->plan->amount;
                                    $taxRate = Helpers::getTaxRate() ?: 0;
                                    $tax     = ($fee * $taxRate) / 100;
                                    $discountAmount = $get('discount_amount') ?: 0;
                                    $total   = $fee + $tax - $discountAmount;

                                    $fee   = round($fee);
                                    $tax   = round($tax);
                                    $total = round($fee + $tax - $discountAmount);
                                    $due   = round($total - ($get('paid_amount') ?: 0));

                                    $set('subscription_fee', $fee);
                                    $set('tax',              $tax);
                                    $set('total_amount',     $total);
                                    $set('due_amount',       $due);
                                } else {
                                    $set('subscription_fee', 0);
                                    $set('tax',              0);
                                    $set('total_amount',     0);
                                    $set('discount_amount',  0);
                                    $set('due_amount',       0);
                                }
                            }
                        )
                        ->required(),
                    DatePicker::make('date')
                        ->label('Date')
                        ->required()
                        ->reactive()
                        ->default(now()),
                    DatePicker::make('due_date')
                        ->label('Due Date')
                        ->required()
                        ->reactive(),
                ])->columns(2),
            Section::make('Amount Summary')
                ->disabled(fn(Get $get): bool => !$get('subscription_id'))
                ->schema([
                    TextInput::make('subscription_fee')
                        ->label('Subscription Fee')
                        ->numeric()
                        ->readOnly()
                        ->default(0)
                        ->prefix(Helpers::getCurrencySymbol())
                        ->required(),
                    TextInput::make('total_amount')
                        ->label('Total Amount')
                        ->numeric()
                        ->readOnly()
                        ->default(0)
                        ->prefix(Helpers::getCurrencySymbol())
                        ->required(),
                    TextInput::make('tax')
                        ->label('Tax (' . Helpers::getTaxRate() . '%)')
                        ->numeric()
                        ->default(0)
                        ->prefix(Helpers::getCurrencySymbol())
                        ->readOnly(),
                    Select::make('discount')
                        ->label('Discount')
                        ->options(Helpers::getDiscounts())
                        ->native(false)
                        ->live()
                        ->reactive()
                        ->placeholder('Select Discount')
                        ->afterStateUpdated(
                            function (Get $get, Set $set) {
                                $fee           = $get('subscription_fee') ?: 0;
                                $tax           = $get('tax') ?: 0;
                                $discountPct   = (int) $get('discount');
                                $discountAmount = Helpers::getDiscountAmount($discountPct, $fee);
                                $total         = $fee + $tax - $discountAmount;

                                $set('discount_amount', round($discountAmount));
                                $set('total_amount', round($total));
                                $set('due_amount', max($total - $get('paid_amount'), 0));
                            }
                        ),
                    TextInput::make('discount_amount')
                        ->label('Discount Amount')
                        ->numeric()
                        ->debounce(300)
                        ->default(0)
                        ->prefix(Helpers::getCurrencySymbol())
                        ->maxValue(fn(Get $get): float => $get('subscription_fee') ?: 0)
                        ->afterStateUpdated(
                            function (Get $get, Set $set, $livewire, TextInput $component) {
                                $livewire->validateOnly($component->getStatePath());

                                $fee            = $get('subscription_fee') ?: 0;
                                $entered        = $get('discount_amount') ?: 0;
                                $clamped        = min(max($entered, 0), $fee);
                                $tax            = $get('tax') ?: 0;
                                $total          = $fee + $tax - $clamped;

                                $set('total_amount', round($total));
                                $set('due_amount', max($total - $get('paid_amount'), 0));
                            }
                        ),
                ])->columns(3),
            Section::make('Payment Details')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('payment_method')
                            ->label('Method')
                            ->placeholder('Select Payment Method')
                            ->options([
                                'cash'   => 'Cash',
                                'cheque' => 'Cheque',
                            ])
                            ->required(),
                        TextInput::make('discount_note')
                            ->label('Discount Note')
                            ->placeholder('E.g. introductory offer'),
                    ]),
                ]),
        ];
    }

    /**
     * Get the Filament table columns for the invoice list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->searchable(),
            TextColumn::make('number')
                ->label('Invoice No.')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('subscription.member.name')
                ->label('Subscription')
                ->description(fn($record): string => $record->subscription->member->code)
                ->weight(FontWeight::Bold)
                ->color('success')
                ->toggleable(isToggledHiddenByDefault: false)
                ->url(fn($record): string => route('filament.admin.resources.subscriptions.view', $record->subscription_id)),
            TextColumn::make('date')
                ->label('Date')
                ->date()
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('due_date')
                ->label('Due Date')
                ->date()
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('subscription_fee')
                ->label('Fee')
                ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('paid_amount')
                ->label('Paid')
                ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                ->searchable(),
            TextColumn::make('tax')
                ->label('Tax')
                ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                ->searchable(),
            TextColumn::make('total_amount')
                ->label('Total')
                ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('due_amount')
                ->label('Due')
                ->formatStateUsing(fn($state): string => Helpers::formatCurrency($state))
                ->searchable(),
            TextColumn::make('status')
                ->badge()
                ->toggleable(isToggledHiddenByDefault: false)
                ->color(fn(string $state): string => match ($state) {
                    'issued' => 'gray',
                    'overdue' => 'warning',
                    'partial' => 'info',
                    'paid' => 'success',
                    'refund' => 'danger',
                    'cancelled' => 'danger',
                })
                ->formatStateUsing(fn(string $state): string => match ($state) {
                    'issued' => 'Issued',
                    'overdue' => 'Overdue',
                    'partial' => 'Partial',
                    'paid' => 'Paid',
                    'refund' => 'Refund',
                    'cancelled' => 'Cancelled',
                    default => ucfirst($state),
                }),
        ];
    }
}
