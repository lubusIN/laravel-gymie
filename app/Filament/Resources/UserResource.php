<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Str;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(User::getForm());
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
            ->columns(User::getTableColumns())
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
                        Tables\Actions\Action::make('inactive')
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
                        Tables\Actions\Action::make('active')
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
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                        Tables\Actions\DeleteAction::make(),
                        Tables\Actions\RestoreAction::make()
                    ])->dropdown(false)
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
                Section::make()
                    ->heading(function (User $record): HtmlString {
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
                        return new HtmlString("Details " . $html);
                    })
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name)
                            ->size(180)
                            ->circular()
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('contact'),
                                TextEntry::make('gender'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date()
                                    ->placeholder('N/A'),
                                TextEntry::make('roles.name')
                                    ->formatStateUsing(
                                        fn($state): string =>
                                        Str::headline($state)
                                    )
                                    ->badge(),
                            ])->columnSpan(4)->columns(3),
                    ])->columns(5),
                Section::make('Location')
                    ->schema([
                        TextEntry::make('address')->label('Address'),
                        Group::make()
                            ->schema([
                                TextEntry::make('country')->label('Country'),
                                TextEntry::make('state')
                                    ->placeholder('N/A'),
                                TextEntry::make('city')
                                    ->placeholder('N/A'),
                                TextEntry::make('pincode')->label('PIN Code'),
                            ])
                            ->columns(4),
                    ]),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
