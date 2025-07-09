<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Filament\Resources\EnquiryResource\RelationManagers\FollowUpsRelationManager;
use App\Models\Enquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Enquiry::getForm());
    }

    /**
     * Get the Filament table columns for the enquiry list view.
     *
     * @return array
     */
    public static function table(Table $table): Tables\Table
    {
        return $table
            ->columns(Enquiry::getTableColumns())
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
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New enquiry')
                    ->hidden(fn() => Enquiry::exists()),
            ])
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
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['date_to'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->visible(fn($record) => in_array($record->status->value, ['lead']))
                            ->color('gray'),
                        Tables\Actions\Action::make('convert_to_member')
                            ->label('Convert to Member')
                            ->icon('heroicon-m-arrows-right-left')
                            ->color('success')
                            ->requiresConfirmation()
                            ->visible(fn(Enquiry $record) => $record->status->value === 'lead')
                            ->url(fn(Enquiry $record) => MemberResource::getUrl(
                                'create',
                                ['enquiry_id' => $record->id],
                            )),
                        Tables\Actions\Action::make('mark_as_lost')
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
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\EditAction::make()->hiddenLabel(),
                        Tables\Actions\DeleteAction::make()
                            ->hiddenLabel()
                    ])->dropdown(false)
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.enquiries.view', $record->id))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
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
                Section::make('Details')
                    ->heading(function (Enquiry $record): HtmlString {
                        $status = $record->status;
                        $html = Blade::render(
                            '<x-filament::badge class="inline-flex" style="margin-left:6px;" :color="$color">
                                        {{ $label }}
                                    </x-filament::badge>',
                            ['color' => $status->getColor(), 'label' => $status->getLabel(),]
                        );
                        return new HtmlString('Details ' . $html);
                    })
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->label('Email')->copyable(),
                        TextEntry::make('contact')->label('Contact')->copyable(),
                        TextEntry::make('gender')->label('Gender'),
                        TextEntry::make('dob')
                            ->label('Date of Birth')
                            ->date(),
                        TextEntry::make('date')
                            ->date(),
                        TextEntry::make('user.name')
                            ->label('Lead Owner')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->url(fn($record): string => route('filament.admin.resources.users.view', $record->user_id)),
                        TextEntry::make('start_by')
                            ->label('Preferred Start Date')
                            ->date()
                            ->placeholder('N/A'),
                    ])
                    ->columns(3)
                    ->columnSpan(4),
                Grid::make()
                    ->columnSpan(4)
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'xl'      => 5,
                    ])
                    ->schema([
                        Section::make('Location')
                            ->columnSpan([
                                'default' => 4,
                                'sm'      => 1,
                                'xl'      => 3,
                            ])
                            ->schema([
                                TextEntry::make('address')->label('Address'),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('country')->label('Country'),
                                        TextEntry::make('state')
                                            ->label('State')
                                            ->placeholder('N/A'),
                                        TextEntry::make('city')
                                            ->label('City')
                                            ->placeholder('N/A'),
                                        TextEntry::make('pincode')->label('PIN Code'),
                                    ])
                                    ->columns(4),
                            ]),
                        Section::make('Preferences')
                            ->columnSpan([
                                'default' => 4,
                                'sm'      => 1,
                                'xl'      => 2,
                            ])
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                TextEntry::make('interested_in')
                                    ->label('Interested In')
                                    ->columnSpanFull()
                                    ->placeholder('N/A'),
                                TextEntry::make('source')
                                    ->label('Source'),
                                TextEntry::make('goal')
                                    ->label('Goal ?'),
                            ]),
                    ])
            ])->columns(4);
    }

    public static function getRelations(): array
    {
        return [
            FollowUpsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEnquiries::route('/'),
            'create' => Pages\CreateEnquiry::route('/create'),
            'edit' => Pages\EditEnquiry::route('/{record}/edit'),
            'view' => Pages\ViewEnquiry::route('/{record}'),
        ];
    }
}
