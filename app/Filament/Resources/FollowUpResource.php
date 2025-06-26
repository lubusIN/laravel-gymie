<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpResource\Pages;
use App\Models\Enquiry;
use App\Models\FollowUp;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(FollowUp::getForm())
            ->columns(1);
    }

    /**
     * Get the Filament table columns for the follow-up list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns(FollowUp::getTableColumns())
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
                Tables\Actions\Action::make('create_enquiry')
                    ->label('New enquiry')
                    ->url(fn() => route('filament.admin.resources.enquiries.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Enquiry::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New follow up')
                    ->createAnother(false)
                    ->modalHeading('New follow up')
                    ->modalWidth('sm')
                    ->visible(fn() => Enquiry::exists() && !FollowUp::exists()),
            ])
            ->filters(static::getTableFilters())
            ->actions(static::getTableActions())
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
                    ->label(function (FollowUp $record): HtmlString {
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
                        TextEntry::make('enquiry.name')
                            ->label('Enquiry')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->url(fn($record): string => route('filament.admin.resources.enquiries.view', $record->enquiry_id)),
                        TextEntry::make('user.name')
                            ->label('Handled By')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->placeholder('N/A')
                            ->url(
                                fn($record) => $record->user_id
                                    ? route('filament.admin.resources.users.view', $record->user_id)
                                    : null
                            ),
                        TextEntry::make('method')
                            ->label('Method'),
                        TextEntry::make('schedule_date')
                            ->label('Schedule Date')
                            ->date('d-m-Y'),
                        TextEntry::make('outcome')
                            ->label('Outcome')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    /**
     * Get table filter definitions.
     */
    public static function getTableFilters(): array
    {
        return [
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
        ];
    }

    /**
     * Get table action definitions.
     */
    public static function getTableActions(): array
    {
        return [
            Tables\Actions\ActionGroup::make([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('heading_actions')
                        ->label('Status')
                        ->visible(fn($record) => in_array($record->status->value, ['pending']))
                        ->disabled()
                        ->color('gray'),
                    Tables\Actions\Action::make('mark_as_done')
                        ->color('success')
                        ->label('Mark as Done')
                        ->modalWidth('sm')
                        ->fillForm(fn(FollowUp $record): array => [
                            'user_id' => $record->user_id,
                            'outcome' => $record->outcome,
                        ])
                        ->form([
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('heading_actions')
                        ->label('Record Actions')
                        ->disabled()
                        ->color('gray'),
                    Tables\Actions\ViewAction::make()
                        ->modalWidth('lg')
                        ->modalCancelAction(false)
                        ->modalAlignment('center')
                        ->infolist(
                            fn(Infolist $infolist): Infolist =>
                            static::infolist($infolist)
                        ),
                    Tables\Actions\EditAction::make()
                        ->modalWidth('sm')
                        ->hidden(fn($record): bool => $record->status->value === 'done'),
                    Tables\Actions\DeleteAction::make(),
                ])->dropdown(false),
            ])
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUps::route('/'),
        ];
    }
}
