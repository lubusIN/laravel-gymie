<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Models\Enquiry;
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
use Filament\Tables;
use Filament\Tables\Table;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

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
                            ->disabled()
                            ->visible(fn($record) => in_array($record->status, ['lead']))
                            ->color('gray'),
                        Tables\Actions\Action::make('mark_as_member')
                            ->icon('heroicon-m-check-circle')
                            ->color('success')
                            ->requiresConfirmation()
                            ->action(fn(Enquiry $record) => tap($record, function ($record) {
                                $record->update(['status' => 'member']);
                                Notification::make()
                                    ->title('Enquiry Marked as Member')
                                    ->success()
                                    ->body("Member has been created")
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status === 'lead'),
                        Tables\Actions\Action::make('mark_lost')
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
                            ->visible(fn($record) => $record->status === 'lead'),
                    ])->dropdown(false),
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\Action::make('heading_actions')
                            ->label('Record Actions')
                            ->disabled()
                            ->color('gray'),
                        Tables\Actions\EditAction::make()->hiddenLabel(),
                        Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    ])->dropdown(false)
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.enquiries.view', $record->id))
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
                Grid::make(5)
                    ->schema([
                        Section::make('Personal Information')
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email')->label('Email'),
                                TextEntry::make('contact')->label('Contact'),
                                TextEntry::make('gender')->label('Gender'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date('d-m-Y'),
                                TextEntry::make('occupation')
                                    ->label('Occupation'),
                            ])
                            ->columns(3)
                            ->columnSpan(3),
                        Section::make('Preferences')
                            ->schema([
                                TextEntry::make('interested_in')
                                    ->label('Interested In'),
                                TextEntry::make('source')
                                    ->label('Source'),
                                TextEntry::make('why_do_you_plan_to_join')
                                    ->label('Reason for Joining'),
                                TextEntry::make('start_by')
                                    ->label('Preferred Start Date')
                                    ->date('d-m-Y')
                                    ->hidden(fn($record) => empty($record->start_by)),
                            ])
                            ->columns(2)
                            ->columnSpan(2),
                    ]),
                Section::make('Address')
                    ->schema([
                        TextEntry::make('address')->label('Address'),
                        Group::make()
                            ->schema([
                                TextEntry::make('country')->label('Country'),
                                TextEntry::make('state')
                                    ->label('State')
                                    ->hidden(fn($record) => empty($record->state)),
                                TextEntry::make('city')
                                    ->label('City')
                                    ->hidden(fn($record) => empty($record->city)),
                                TextEntry::make('pincode')->label('PIN Code'),
                            ])
                            ->columns(4),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
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
