<?php

namespace App\Filament\Resources\FollowUps\Tables;

use App\Filament\Resources\FollowUps\FollowUpResource;
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
use App\Models\Enquiry;
use App\Models\FollowUp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Actions\ViewAction;
use App\Models\User;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Blade;

class FollowUpTable
{
    /**
     * Configure the follow-up table schema.
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
                TextColumn::make('enquiry.name')
                    ->searchable()
                    ->label('Enquiry')
                    ->sortable(),
                TextColumn::make('user.name')
                    ->searchable()
                    ->label('Handled By')
                    ->placeholder('N/A')
                    ->sortable(),
                TextColumn::make('method')
                    ->label('Method')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('schedule_date')
                    ->searchable()
                    ->date('d-m-Y')
                    ->label('Schedule Date')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('status')
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('outcome')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('N/A')
                    ->limit(40)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
            ])
            ->emptyStateIcon(
                !Enquiry::exists() ? 'heroicon-o-phone' : 'heroicon-o-arrow-path-rounded-square'
            )
            ->emptyStateHeading(function ($livewire): string {
                // If no enquiry exist
                if (!Enquiry::exists()) {
                    return 'No Enquiries';
                }

                $dates       = $livewire->getTableFilterState('date') ?? [];
                [$from, $to] = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab         = $livewire->activeTab;
                $heading     = [
                    'pending'  => 'No Pending Follow Ups',
                    'done'     => 'No Done Follow Ups',
                ][$tab] ?? 'No Follow Ups';

                if (!$from && !$to) {
                    return $heading;
                }

                if ($tab === 'all') {
                    return 'No Follow Ups in Date Range';
                }

                return Enquiry::where('status', $tab)->exists()
                    ? ($heading . ' in Date Range')
                    : $heading;
            })
            ->emptyStateDescription(function ($livewire): ?string {
                // If no enquiries exist
                if (!Enquiry::exists()) {
                    return 'Create a enquiry to get started.';
                }

                $dates               = $livewire->getTableFilterState('date') ?? [];
                [$fromRaw, $toRaw]   = [$dates['date_from'] ?? null, $dates['date_to'] ?? null];
                $tab                 = $livewire->activeTab;
                $defaultDescriptions = [
                    'pending' => 'There are no follow ups marked as pending.',
                    'done'    => 'There are no follow ups marked as done.',
                ];

                if (!$fromRaw && !$toRaw) {
                    return $defaultDescriptions[$tab] ?? 'Create a follow up to get started.';
                }

                $from = $fromRaw ? Carbon::parse($fromRaw)->format('d-m-Y') : 'the beginning';
                $to = $toRaw ? Carbon::parse($toRaw)->format('d-m-Y') : 'today';

                if ($tab === 'all') {
                    return "We found no follow ups created between {$from} and {$to}.";
                }

                if (!FollowUp::where('status', $tab)->exists()) {
                    return $defaultDescriptions[$tab] ?? 'Create a follow up to get started.';
                }

                return "We found no {$tab} follow up between {$from} and {$to}.";
            })
            ->emptyStateActions([
                Action::make('create_enquiry')
                    ->label('New enquiry')
                    ->url(fn() => route('filament.admin.resources.enquiries.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Enquiry::exists()),
                CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New follow up')
                    ->createAnother(false)
                    ->modalHeading('New follow up')
                    ->modalWidth('sm')
                    ->visible(fn() => Enquiry::exists() && !FollowUp::exists()),
            ])
            ->filters(static::getTableFilters())
            ->recordActions(static::getTableActions())
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Get table filter definitions.
     */
    public static function getTableFilters(): array
    {
        return [
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
        ];
    }

    /**
     * Get table action definitions.
     */
    public static function getTableActions(): array
    {
        return [
            ActionGroup::make([
                ActionGroup::make([
                    Action::make('heading_actions')
                        ->label('Status')
                        ->visible(fn($record) => in_array($record->status->value, ['pending']))
                        ->disabled()
                        ->color('gray'),
                    Action::make('mark_as_done')
                        ->color('success')
                        ->label('Mark as Done')
                        ->modalWidth('sm')
                        ->fillForm(fn(FollowUp $record): array => [
                            'user_id' => $record->user_id,
                            'outcome' => $record->outcome,
                        ])
                        ->schema([
                            Select::make('user_id')
                                ->label('Handled By')
                                ->relationship(name: 'user', titleAttribute: 'name')
                                ->placeholder('Select Handler')
                                ->getOptionLabelFromRecordUsing(function (User $record): string {
                                    $name = html_entity_decode($record->name, ENT_QUOTES, 'UTF-8');
                                    $url  = !empty($record->photo) ? e($record->photo) : "https://ui-avatars.com/api/?background=000&color=fff&name={$name}";
                                    return Blade::render(
                                        '<div class="flex items-center gap-2 h-9">
                                                <x-filament::avatar src="{{ $url }}" alt="{{ $name }}" size="sm" />
                                                <span class="ml-2">{{ $name }}</span>
                                            </div>',
                                        compact('url', 'name')
                                    );
                                })
                                ->allowHtml()
                                ->required(),
                            Textarea::make('outcome')
                                ->placeholder('Not interested, etc.')
                                ->label('Outcome')
                                ->required(),
                        ])
                        ->action(function (array $data, FollowUp $record): void {
                            $record->update([
                                'user_id' => $data['user_id'],
                                'outcome' => $data['outcome'],
                                'status' => 'done'
                            ]);
                            Notification::make()
                                ->title('Marked as done')
                                ->success()
                                ->send();
                        })
                        ->visible(fn($record) => $record->status->value === 'pending'),
                ])->dropdown(false),
                ActionGroup::make([
                    Action::make('heading_actions')
                        ->label('Record Actions')
                        ->disabled()
                        ->color('gray'),
                    ViewAction::make()
                        ->modalWidth('lg')
                        ->modalCancelAction(false)
                        ->modalAlignment('center')
                        ->schema(
                            fn(Schema $schema): Schema =>
                            FollowUpResource::infolist($schema)
                        ),
                    EditAction::make()
                        ->modalWidth('sm')
                        ->hidden(fn($record): bool => $record->status->value === 'done'),
                    DeleteAction::make(),
                ])->dropdown(false),
            ])
        ];
    }
}
