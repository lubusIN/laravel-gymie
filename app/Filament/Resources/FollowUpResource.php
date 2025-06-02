<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpResource\Pages;
use App\Models\Enquiry;
use App\Models\FollowUp;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
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
            ->emptyStateHeading(!Enquiry::exists() ? 'No Enquiries Found' : 'No Follow-Ups Found')
            ->emptyStateDescription(!Enquiry::exists() ? 'Create an Enquiry to get started' : 'Create Follow-Ups to get started.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('New Enquiry')
                    ->url(fn() => route('filament.admin.resources.enquiries.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Enquiry::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New Follow-Up')
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
                    Tables\Actions\Action::make('mark_as_done')
                        ->color('success')
                        ->icon('heroicon-m-check-circle')
                        ->requiresConfirmation()
                        ->action(fn(FollowUp $record) => tap($record, function ($record) {
                            $record->update(['status' => 'done']);
                            Notification::make()
                                ->title('Mark as done')
                                ->success()
                                ->send();
                        }))
                        ->visible(fn($record) => $record->status === 'pending'),
                    Tables\Actions\EditAction::make()->hiddenLabel(),
                    Tables\Actions\DeleteAction::make()->hiddenLabel(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUps::route('/'),
            'create' => Pages\CreateFollowUp::route('/create'),
            'edit' => Pages\EditFollowUp::route('/{record}/edit'),
        ];
    }
}
