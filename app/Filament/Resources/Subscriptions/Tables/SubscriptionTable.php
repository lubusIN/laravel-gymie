<?php

namespace App\Filament\Resources\Subscriptions\Tables;

use App\Filament\Resources\Subscriptions\SubscriptionResource;
use App\Models\Member;
use App\Models\Plan;
use App\Models\Subscription;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionTable
{
    /**
     * Configure the subscription table schema.
     *
     * @param Table $table
     * @return Table
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
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
            ])
            ->emptyStateIcon(
                ! Member::exists()
                    ? 'heroicon-o-user-group'
                    : (! Plan::exists()
                        ? 'heroicon-o-pencil-square'
                        : 'heroicon-o-ticket'
                    )
            )
            ->emptyStateHeading(function ($livewire): string {
                // If no members exist
                if (!Member::exists()) {
                    return 'No Members';
                }

                // If no plans exist
                if (!Plan::exists()) {
                    return 'No Plans';
                }

                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'ongoing'  => 'No Ongoing Subscriptions',
                    'expiring' => 'No Expiring Subscriptions',
                    'expired'  => 'No Expired Subscriptions',
                ][$tab] ?? 'No Subscriptions';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Subscriptions in Date Range';
                }

                return Subscription::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                // If no members exist
                if (!Member::exists()) {
                    return 'Create a member to get started.';
                }
                // If no plans exist
                if (!Plan::exists()) {
                    return 'Create a plan to get started.';
                }

                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'ongoing'  => 'There are no subscriptions currently ongoing.',
                    'expiring' => 'There are no subscriptions marked as expiring.',
                    'expired'  => 'There are no subscriptions that have expired.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a subscription to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no subscriptions created between {$from} and {$to}.";
                }

                if (!Subscription::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a subscription to get started.';
                }

                return "We found no {$tab} subscriptions between {$from} and {$to}.";
            })
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New subscription')
                    ->visible(fn($livewire) => Member::exists() && Plan::exists() && (!Subscription::exists() || !$livewire->getOwnerRecord()->subscriptions()->exists())),
                Action::make('create_member')
                    ->icon('heroicon-o-plus')
                    ->label('New member')
                    ->url(fn() => route('filament.admin.resources.members.create'))
                    ->hidden(fn() => Member::exists()),
                Action::make('create_plan')
                    ->icon('heroicon-o-plus')
                    ->label('New plan')
                    ->url(fn() => route('filament.admin.resources.plans.create'))
                    ->hidden(fn() => Plan::exists()),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date_from')
                            ->native(false)
                            ->suffixIcon('heroicon-m-calendar-days'),
                        DatePicker::make('date_to')
                            ->native(false)
                            ->suffixIcon('heroicon-m-calendar-days'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('heading')
                            ->label('Change Status')
                            ->disabled()
                            ->color('gray')
                            ->hidden(fn($record) => $record->status->value == 'expired'),
                        Action::make('mark_expiring')
                            ->label('Mark as expiring')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->action(fn(Subscription $record) => tap($record, function ($r) {
                                $r->update(['status' => 'expiring']);
                                Notification::make()
                                    ->title('Subscription marked as expiring')
                                    ->warning()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value == 'ongoing'),
                        Action::make('mark_expired')
                            ->label('Mark as expired')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(fn(Subscription $record) => tap($record, function ($r) {
                                $r->update(['status' => 'expired']);
                                Notification::make()
                                    ->title('Subscription marked as expired')
                                    ->danger()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value !== 'expired'),
                    ])->dropdown(false),
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        ViewAction::make()
                            ->url(fn($record) => SubscriptionResource::getUrl('view', ['record' => $record])),
                        EditAction::make()
                            ->hiddenLabel()
                            ->hidden(fn($record) => $record->status->value === 'expired')
                            ->url(fn($record) => SubscriptionResource::getUrl('edit', ['record' => $record])),
                        DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn(Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }
}
