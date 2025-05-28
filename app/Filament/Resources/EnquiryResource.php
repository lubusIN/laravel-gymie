<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EnquiryResource\Pages;
use App\Models\Enquiry;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
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
     * Get the Filament table columns for the estimate list view.
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
                    Tables\Actions\Action::make('mark_as_member')
                        ->icon('heroicon-m-check-circle')
                        ->action(fn(Enquiry $record) => tap($record, function ($record) {
                            $record->update(['status' => 'member']);
                            Notification::make()
                                ->title('Mark as Member')
                                ->success()
                                ->body("Member has been created")
                                ->send();
                        }))
                        ->visible(fn($record) => $record->status === 'lead'),
                    Tables\Actions\Action::make('mark_lost')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(fn(Enquiry $record) => tap($record, function ($record) {
                            $record->update(['status' => 'lost']);
                            Notification::make()
                                ->title('Mark as Lost') 
                                ->success()
                                ->iconColor('danger')
                                ->send();
                        }))
                        ->visible(fn($record) => $record->status === 'lead'),
                    Tables\Actions\EditAction::make()->hiddenLabel(),
                    Tables\Actions\DeleteAction::make()->hiddenLabel(),
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.enquiries.view', $record->id))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
