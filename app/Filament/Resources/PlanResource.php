<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Helpers\Helpers;
use App\Models\Plan;
use App\Models\Service;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Plan::getForm());
    }

    /**
     * Get the Filament table columns for the plan list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return $table
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
                Tables\Actions\Action::make('manage_service')
                    ->label('Manage services')
                    ->url(fn() => route('filament.admin.resources.services.index'))
                    ->icon('heroicon-o-arrow-right')
                    ->iconPosition('after')
                    ->hidden(fn() => Service::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New plan')
                    ->modalAlignment('center')
                    ->modalWidth('xl')
                    ->modalHeading('New plan')
                    ->createAnother(false)
                    ->visible(fn() => Service::exists() && !Plan::exists()),
            ])
            ->columns(Plan::getTableColumns())
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
                                fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\Action::make('mark_as_active')
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
                        Tables\Actions\Action::make('mark_as_inactive')
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
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\ViewAction::make()
                            ->modalWidth('xl')
                            ->modalCancelAction(false)
                            ->modalAlignment('center'),
                        Tables\Actions\EditAction::make()
                            ->modalAlignment('center')
                            ->modalWidth('xl'),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
                Fieldset::make('')
                    ->label(function (Plan $record): HtmlString {
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
                        return new HtmlString($html);
                    })
                    ->schema([
                        TextEntry::make('code')
                            ->label('Code')
                            ->columnSpan(1),
                        TextEntry::make('name')
                            ->label('Name')
                            ->columnSpan(2),
                        TextEntry::make('service.name')
                            ->label('Service'),
                        TextEntry::make('days')
                            ->label('Days'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(Helpers::getCurrencyCode()),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
        ];
    }
}
