<?php

namespace App\Filament\Resources\Enquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Tables\Filters\Filter;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Enquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class EnquiryTable
{
    /**
     * Configure the enquiry table schema.
     *
     * @param Table $table
     * @return Table
     */
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable()->toggleable(isToggledHiddenByDefault: true)->label('ID'),
                TextColumn::make('name')->searchable()->sortable()->label('Name'),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: false)->label('Email'),
                TextColumn::make('contact')->toggleable(isToggledHiddenByDefault: true)->label('Contact'),
                TextColumn::make('date')->sortable()->date('d-m-Y')->toggleable(isToggledHiddenByDefault: true)->label('Date'),
                TextColumn::make('start_by')->date('d-m-Y')->toggleable(isToggledHiddenByDefault: true)->label('Start by'),
                TextColumn::make('status')
                    ->badge()
                    ->label('Status')
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateIcon('heroicon-o-phone')
            ->emptyStateHeading(function ($livewire): string {
                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'lead'   => 'No Lead Enquiries',
                    'lost'   => 'No Lost Enquiries',
                    'member' => 'No Member Enquiries',
                ][$tab] ?? 'No Enquiries';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Enquiries in Date Range';
                }

                return Enquiry::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'lead'   => 'Looks like there are no lead enquiries right now.',
                    'lost'   => 'No enquiries have been marked as lost yet.',
                    'member' => 'No member enquiries to display at the moment.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a enquiry to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no enquiries created between {$from} and {$to}.";
                }

                if (!Enquiry::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a enquiry to get started.';
                }

                return "We found no {$tab} enquiries between {$from} and {$to}.";
            })
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New enquiry')
                    ->hidden(fn() => Enquiry::exists()),
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
            ->recordActions([
                ActionGroup::make([
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->visible(fn($record) => in_array($record->status->value, ['lead']))
                            ->color('gray'),
                        Action::make('convert_to_member')
                            ->label('Convert to Member')
                            ->icon('heroicon-m-arrows-right-left')
                            ->color('success')
                            ->requiresConfirmation()
                            ->visible(fn(Enquiry $record) => $record->status->value === 'lead')
                            ->url(fn(Enquiry $record) => MemberResource::getUrl(
                                'create',
                                ['enquiry_id' => $record->id],
                            )),
                        Action::make('mark_as_lost')
                            ->label('Mark as Lost')
                            ->icon('heroicon-m-x-circle')
                            ->color('danger')
                            ->requiresConfirmation()
                            ->action(fn(Enquiry $record) => tap($record, function ($record) {
                                $record->update(['status' => 'lost']);
                                Notification::make()
                                    ->title('Enquiry Marked as Lost')
                                    ->success()
                                    ->icon('heroicon-m-no-symbol')
                                    ->iconColor('danger')
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status->value === 'lead'),
                    ])->dropdown(false),
                    ActionGroup::make([
                        Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        EditAction::make()->hiddenLabel(),
                        DeleteAction::make()
                            ->hiddenLabel()
                    ])->dropdown(false)
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.enquiries.view', $record->id))
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                ]),
            ]);
    }
}
