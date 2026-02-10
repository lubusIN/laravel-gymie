<?php

namespace App\Filament\Resources\Members\Tables;

use Carbon\Carbon;
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
use App\Models\Member;
use Filament\Tables\Columns\ImageColumn;

class MemberTable
{
    /**
     * Configure the member table schema.
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
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(fn(Member $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name),
                TextColumn::make('code')
                    ->searchable(),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('email')
                    ->searchable()
                    ->label('Email'),
                TextColumn::make('gender')
                    ->searchable()
                    ->label('Gender'),
                TextColumn::make('contact')
                    ->searchable()
                    ->label('Contact'),
                TextColumn::make('emergency_contact')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Emergency Contact'),
                TextColumn::make('created_at')
                    ->sortable()
                    ->date('d-m-Y')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Date'),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status'),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading(function ($livewire): string {
                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'active' => 'No Active Members',
                    'inactive' => 'No Inactive Members',
                ][$tab] ?? 'No Members';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Members in Date Range';
                }

                return Member::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'active' => 'There are no members currently active.',
                    'inactive' => 'There are no members marked as inactive.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a member to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no members created between {$from} and {$to}.";
                }

                if (!Member::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a member to get started.';
                }

                return "We found no {$tab} members between {$from} and {$to}.";
            })
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New member')
                    ->hidden(fn() => Member::exists()),
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
                        Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->color('gray'),
                        Action::make('mark_as_active')
                            ->color('success')
                            ->label('Mark as active')
                            ->requiresConfirmation()
                            ->action(fn(Member $record) => tap($record, function ($record) {
                                $record->update(['status' => 'active']);
                                Notification::make()
                                    ->title('Member has been activated')
                                    ->success()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'inactive'),
                        Action::make('mark_as_inactive')
                            ->color('danger')
                            ->label('Mark as inactive')
                            ->requiresConfirmation()
                            ->action(fn(Member $record) => tap($record, function ($record) {
                                $record->update(['status' => 'inactive']);
                                Notification::make()
                                    ->title('Member has been deactivated')
                                    ->danger()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'active'),
                    ])->dropdown(false),
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        ViewAction::make(),
                        EditAction::make()->hiddenLabel(),
                        DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ]),
            ])->recordUrl(fn($record): string => route('filament.admin.resources.members.view', $record->id))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
