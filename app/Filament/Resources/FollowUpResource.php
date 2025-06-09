<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpResource\Pages;
use App\Models\Enquiry;
use App\Models\FollowUp;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(FollowUp::getForm());
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
            ->defaultSort('id', 'desc')
            ->emptyStateIcon(!Enquiry::exists() ? 'heroicon-o-phone' : 'heroicon-o-arrow-path-rounded-square')
            ->emptyStateHeading(!Enquiry::exists() ? 'No Enquiries' : 'No Follow Ups')
            ->emptyStateDescription(!Enquiry::exists() ? 'Create an enquiry to get started' : 'Create follow-ups to get started.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('New enquiry')
                    ->url(fn() => route('filament.admin.resources.enquiries.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Enquiry::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New follow up')
                    ->visible(fn() => Enquiry::exists()),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Status')
                            ->visible(fn($record) => in_array($record->status, ['pending']))
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\Action::make('mark_as_done')
                            ->color('success')
                            ->label('Mark as Done')
                            ->requiresConfirmation()
                            ->action(fn(FollowUp $record) => tap($record, function ($record) {
                                $record->update(['status' => 'done']);
                                Notification::make()
                                    ->title('Marked as done')
                                    ->success()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status === 'pending'),
                    ])->dropdown(false),
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\EditAction::make()->hiddenLabel(),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false),
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.follow-ups.view', $record->id))
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
                Section::make('Details')
                    ->schema([
                        TextEntry::make('enquiry.name')
                            ->label('Enquirer'),
                        TextEntry::make('date')
                            ->label('Date')
                            ->date('d-m-Y'),
                        TextEntry::make('outcome')
                            ->label('Outcome'),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->icon(fn($state) => $state === 'done' ? 'heroicon-m-check-circle' : 'heroicon-m-x-circle')
                            ->color(fn($state) => $state === 'done' ? 'success' : 'danger'),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUps::route('/'),
            'create' => Pages\CreateFollowUp::route('/create'),
            'edit' => Pages\EditFollowUp::route('/{record}/edit'),
            'view' => Pages\ViewFollowUp::route('/{record}')
        ];
    }
}
