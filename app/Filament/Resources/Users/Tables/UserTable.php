<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\Action;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\RestoreAction;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Str;

class UserTable
{
    /**
     * Configure the user table schema.
     *
     * @param Table $table
     * @return Table
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('contact')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('gender')->searchable(),
                TextColumn::make('roles.name')
                    ->placeholder('N/A')
                    ->searchable()
                    ->formatStateUsing(
                        fn($state): string =>
                        Str::headline($state)
                    )
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->emptyStateIcon('heroicon-o-users')
            ->emptyStateHeading(function ($livewire): string {
                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'active' => 'No Active Users',
                    'inactive' => 'No Inactive Users',
                ][$tab] ?? 'No Users';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Users in Date Range';
                }

                return User::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'active' => 'There are no users currently active.',
                    'inactive' => 'There are no users marked as inactive.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a user to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no users created between {$from} and {$to}.";
                }

                if (!User::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a user to get started.';
                }

                return "We found no {$tab} users between {$from} and {$to}.";
            })
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
                                fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->color('gray'),
                        Action::make('inactive')
                            ->label('Mark as Inactive')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->icon('heroicon-s-x-circle')
                            ->action(fn(User $record) => tap($record, function ($record) {
                                $record->update(['status' => 'inactive']);
                                Notification::make()
                                    ->title('Inactive')
                                    ->danger()
                                    ->body("{$record->name} has been inactivated.")
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'active'),
                        Action::make('active')
                            ->label('Mark as Active')
                            ->color('success')
                            ->requiresConfirmation()
                            ->icon('heroicon-s-check-circle')
                            ->action(fn(User $record) => tap($record, function ($record) {
                                $record->update(['status' => 'active']);
                                Notification::make()
                                    ->title('Active')
                                    ->success()
                                    ->body("{$record->name} has been activated.")
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'inactive'),
                    ])->dropdown(false),
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        ViewAction::make(),
                        EditAction::make(),
                        DeleteAction::make(),
                        RestoreAction::make()
                    ])->dropdown(false)
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
