<?php

namespace App\Filament\Resources\Plans\Tables;

use App\Helpers\Helpers;
use App\Models\Plan;
use App\Models\Service;
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
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;

class PlanTable
{
    /**
     * Configure the plan table schema.
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
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
                    ->searchable()
                    ->label('Code'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Name'),
                TextColumn::make('description')
                    ->searchable()
                    ->label('Description'),
                TextColumn::make('service.name')
                    ->searchable()
                    ->label('Service'),
                TextColumn::make('days')
                    ->searchable()
                    ->label('Days'),
                TextColumn::make('amount')
                    ->searchable()
                    ->label('Amount')
                    ->money(Helpers::getCurrencyCode()),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status'),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateIcon(
                !Service::exists() ? 'heroicon-o-cog-8-tooth' : 'heroicon-o-pencil-square'
            )
            ->emptyStateHeading(function ($livewire): string {
                // If no service exist
                if (!Service::exists()) {
                    return 'No Services';
                }

                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'active'  => 'No Active Plans',
                    'inactive'     => 'No Inactive Plans',
                ][$tab] ?? 'No Plans';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Plans in Date Range';
                }

                return Plan::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                // If no services exist
                if (!Service::exists()) {
                    return 'Go to Services to create your first service.';
                }

                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'active'   => 'There are no Plans marked as active.',
                    'inactive' => 'There are no Plans marked as inactive.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a Plan to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no Plans created between {$from} and {$to}.";
                }

                if (!Plan::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a Plan to get started.';
                }

                return "We found no {$tab} plan between {$from} and {$to}.";
            })
            ->emptyStateActions([
                Action::make('manage_service')
                    ->label('Manage services')
                    ->url(fn() => route('filament.admin.resources.services.index'))
                    ->icon('heroicon-o-arrow-right')
                    ->iconPosition('after')
                    ->hidden(fn() => Service::exists()),
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New plan')
                    ->modalAlignment('center')
                    ->modalWidth('xl')
                    ->modalHeading('New plan')
                    ->createAnother(false)
                    ->visible(fn() => Service::exists() && !Plan::exists()),
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
                        Action::make('mark_as_active')
                            ->color('success')
                            ->label('Mark as active')
                            ->requiresConfirmation()
                            ->action(fn(Plan $record) => tap($record, function ($record) {
                                $record->update(['status' => 'active']);
                                Notification::make()
                                    ->title('Plan has been activated')
                                    ->success()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'inactive'),
                        Action::make('mark_as_inactive')
                            ->color('danger')
                            ->label('Mark as inactive')
                            ->requiresConfirmation()
                            ->action(fn(Plan $record) => tap($record, function ($record) {
                                $record->update(['status' => 'inactive']);
                                Notification::make()
                                    ->title('Plan has been deactivated')
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
                        ViewAction::make()
                            ->modalWidth('xl')
                            ->modalCancelAction(false)
                            ->modalAlignment('center'),
                        EditAction::make()
                            ->modalAlignment('center')
                            ->modalWidth('xl'),
                        DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
