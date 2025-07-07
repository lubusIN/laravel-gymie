<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MemberResource\Pages;
use App\Filament\Resources\MemberResource\RelationManagers\SubscriptionsRelationManager;
use App\Models\Member;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
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
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Member::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(Member::getTableColumns())
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
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New member')
                    ->hidden(fn() => Member::exists()),
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
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Status')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\Action::make('mark_as_active')
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
                        Tables\Actions\Action::make('mark_as_inactive')
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
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make()->hiddenLabel(),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ]),
            ])->recordUrl(fn($record): string => route('filament.admin.resources.members.view', $record->id))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class
        ];
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->heading(function (Member $record): HtmlString {
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
                        return new HtmlString('Details ' . $html);
                    })
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->defaultImageUrl(fn(Member $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name)
                            ->size(180)
                            ->circular()
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Member Code'),
                                TextEntry::make('name'),
                                TextEntry::make('gender')->label('Gender'),
                                TextEntry::make('email'),
                                TextEntry::make('contact'),
                                TextEntry::make('emergency_contact')->placeholder('N/A'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date('d-m-Y'),
                                TextEntry::make('source')
                                    ->label('Source')
                                    ->placeholder('N/A'),
                                TextEntry::make('goal')
                                    ->label('Goal ?')
                                    ->placeholder('N/A'),
                                TextEntry::make('health_issue')
                                    ->label('Health Issue')
                                    ->placeholder('N/A'),
                            ])->columnSpan(4)->columns(3),
                    ])->columns(5),
                Section::make('Location')
                    ->columns(3)
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
                            ->columnSpan(2)
                            ->columns(4),
                    ]),

            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMembers::route('/'),
            'create' => Pages\CreateMember::route('/create'),
            'edit' => Pages\EditMember::route('/{record}/edit'),
            'view' => Pages\ViewMember::route('/{record}')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
