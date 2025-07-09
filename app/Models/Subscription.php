<?php

namespace App\Models;

use App\Enums\Status;
use App\Filament\Resources\MemberResource\Pages\CreateMember;
use App\Helpers\Helpers;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Filament\Resources\MemberResource\RelationManagers\SubscriptionsRelationManager;

class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'member_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'status'     => Status::class
    ];

    protected $dates = ['deleted_at', 'start_date', 'end_date'];

    /**
     * Get the invoices for the subscription.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * The member who owns this subscription.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * The plan this subscription is for.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    /**
     * Get the Filament form schema for the subscription.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            Group::make()
                ->columns(6)
                ->columnSpanFull()
                ->schema([
                    Select::make('member_id')
                        ->columnSpan(2)
                        ->relationship('member', 'name')
                        ->placeholder('Select a member')
                        ->getOptionLabelFromRecordUsing(fn(Member $record): string => "{$record->code} - {$record->name}")
                        ->hiddenOn([SubscriptionsRelationManager::class, CreateMember::class])
                        ->required(),
                    Select::make('plan_id')
                        ->columnSpan(fn($livewire) => ($livewire instanceof SubscriptionsRelationManager ||
                            $livewire instanceof CreateMember)
                            ? 4
                            : 2)
                        ->relationship('plan', 'name')
                        ->placeholder('Select a plan')
                        ->searchable(['code', 'name'])
                        ->reactive()
                        ->getOptionLabelFromRecordUsing(fn(Plan $record): string => sprintf(
                            '%s – %s (%s%s | %d days)',
                            $record->code,
                            $record->name,
                            Helpers::getCurrencySymbol(),
                            round($record->amount),
                            $record->days,
                        ))
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $plan    = \App\Models\Plan::find($get('plan_id'));
                            $fee     = round($plan->amount ?? 0);
                            $taxRate = Helpers::getTaxRate() ?: 0;
                            $tax     = round(($fee * $taxRate) / 100);

                            // grab current invoices array (if any)
                            $invoices = $get('invoices') ?? [];

                            foreach ($invoices as $index => $invoice) {
                                $discount = $invoice['discount_amount'] ?? 0;
                                $paid     = $invoice['paid_amount']     ?? 0;
                                $total    = round($fee + $tax - $discount);
                                $due      = round(max($total - $paid, 0));

                                // set each nested invoice field
                                $set("invoices.{$index}.subscription_fee", $fee);
                                $set("invoices.{$index}.tax",              $tax);
                                $set("invoices.{$index}.total_amount",     $total);
                                $set("invoices.{$index}.due_amount",       $due);
                            }

                            $set('end_date', Helpers::calculateSubscriptionEndDate(
                                $get('start_date'),
                                $get('plan_id'),
                            ));
                        })
                        ->required(),
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->live()
                        ->required()
                        ->default(now())
                        ->before('end_date')
                        ->reactive()                         // <— also reactive
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $set('end_date', Helpers::calculateSubscriptionEndDate(
                                $get('start_date'),
                                $get('plan_id'),
                            ));
                        }),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->live()
                        ->required()
                        ->after('start_date')
                        ->disabled()
                        ->dehydrated()
                        ->reactive()
                        ->afterStateHydrated(function (Get $get, Set $set) {
                            $set('end_date', Helpers::calculateSubscriptionEndDate(
                                $get('start_date'),
                                $get('plan_id'),
                            ));
                        }),
                ]),
            Section::make('Invoice Details')
                ->visibleOn('create')
                ->schema(
                    [
                        Repeater::make('invoices')
                            ->relationship('invoices')
                            ->itemLabel('')
                            ->hiddenLabel()
                            ->columnSpanFull()
                            ->maxItems(1)
                            ->deletable(false)
                            ->columns(4)
                            ->extraAttributes(['class' => 'rmv_rept-space'])
                            ->schema([
                                Group::make()
                                    ->columns(3)
                                    ->columnSpan(3)
                                    ->schema([
                                        TextInput::make('number')
                                            ->label('Invoice No.')
                                            ->required()
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated()
                                            ->unique('invoices', 'number')
                                            ->default(fn(Get $get) => Helpers::generateLastNumber(
                                                'invoice',
                                                Invoice::class,
                                                $get('date')
                                            )),
                                        DatePicker::make('date')
                                            ->label('Date')
                                            ->required()
                                            ->reactive()
                                            ->default(now()),
                                        DatePicker::make('due_date')
                                            ->label('Due Date')
                                            ->required()
                                            ->reactive(),
                                        Select::make('discount')
                                            ->label('Discount')
                                            ->options(Helpers::getDiscounts())
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
                                        Textarea::make('discount_note')
                                            ->label('Discount Note')
                                            ->placeholder('E.g. introductory offer'),
                                        Radio::make('payment_method')
                                            ->label('Payment Method')
                                            ->options([
                                                'cash'   => 'Cash',
                                                'cheque' => 'Cheque',
                                            ])
                                            ->default('cash')
                                            ->inline()
                                            ->inlineLabel(false)
                                            ->required(),
                                    ]),
                                Fieldset::make('Summary')
                                    ->columns(1)
                                    ->columnSpan(1)
                                    ->schema([
                                        TextInput::make('subscription_fee')
                                            ->label('Subscription Fee')
                                            ->numeric()
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(0)
                                            ->prefix(Helpers::getCurrencySymbol())
                                            ->required(),
                                        TextInput::make('tax')
                                            ->label('Tax (' . Helpers::getTaxRate() . '%)')
                                            ->numeric()
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(0)
                                            ->prefix(Helpers::getCurrencySymbol())
                                            ->readOnly(),
                                        TextInput::make('total_amount')
                                            ->label('Total Amount')
                                            ->numeric()
                                            ->readOnly()
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(0)
                                            ->prefix(Helpers::getCurrencySymbol())
                                            ->required(),
                                    ]),
                            ]),
                    ]
                )
        ];
    }

    /**
     * Get the Filament table columns for the subscription list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('member.name')
                ->label('Member')
                ->description(fn($record): string => $record->member->code),
            TextColumn::make('plan.name')
                ->label('Plan')
                ->description(fn($record): string => $record->plan->code),
            TextColumn::make('start_date')
                ->label('Start Date')
                ->date(),
            TextColumn::make('end_date')
                ->label('End Date')
                ->date(),
            TextColumn::make('created_at')
                ->date()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('status')
                ->badge(),
        ];
    }
}
