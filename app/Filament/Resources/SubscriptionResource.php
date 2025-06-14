<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Member;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Subscription::getForm());
    }

    /**
     * Define the table for listing records in the resource.
     *
     * @param \Filament\Tables\Table $table
     * @return \Filament\Tables\Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(Subscription::getTableColumns())
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
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New subscription')
                    ->visible(fn() => Member::exists() && Plan::exists() && !Subscription::exists()),
                Tables\Actions\Action::make('create_member')
                    ->icon('heroicon-o-plus')
                    ->label('New member')
                    ->url(fn() => route('filament.admin.resources.members.create'))
                    ->hidden(fn() => Member::exists()),
                Tables\Actions\Action::make('create_plan')
                    ->icon('heroicon-o-plus')
                    ->label('New plan')
                    ->url(fn() => route('filament.admin.resources.plans.create'))
                    ->hidden(fn() => Plan::exists()),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('date')
                    ->form([
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
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading')
                            ->label('Change Status')
                            ->disabled()
                            ->color('gray')
                            ->hidden(fn($record) => $record->status == 'expired'),
                        Tables\Actions\Action::make('mark_expiring')
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
                            ->visible(fn($record) => $record->status == 'ongoing'),
                        Tables\Actions\Action::make('mark_expired')
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
                            ->visible(fn($record) => $record->status !== 'expired'),
                    ])->dropdown(false),
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()
                            ->hiddenLabel()
                            ->hidden(fn($record) => $record->status === 'expired'),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
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
     * Define the infolist schema for the resource.
     *
     * @param \Filament\Infolists\Infolist $infolist
     * @return \Filament\Infolists\Infolist
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('')
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('member')
                            ->label('Member')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->formatStateUsing(fn($record): string => "{$record->member->code} – {$record->member->name}")
                            ->url(fn($record): string => route('filament.admin.resources.members.view', $record->member_id)),
                        TextEntry::make('plan')
                            ->label('Plan')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->formatStateUsing(fn($record): string => "{$record->plan->code} – {$record->plan->name}")
                            ->url(fn($record): string => route('filament.admin.resources.plans.view', $record->plan_id)),
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->date(),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'ongoing'  => 'success',
                                'expiring' => 'warning',
                                'expired'  => 'danger',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'ongoing'  => 'Ongoing',
                                'expiring' => 'Expiring',
                                'expired'  => 'Expired',
                            }),
                    ])->columns(3),
            ]);
    }

    /**
     * Get the list of relations for this resource.
     *
     * @return array<string, string>
     */
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    /**
     * Get the list of pages for this resource.
     *
     * @return array<string, \Filament\Resources\Pages\Page>
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query builder for this resource, excluding global scopes.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
