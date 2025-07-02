<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Helpers\Helpers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
            ->actions([
                Tables\Actions\ActionGroup::make([
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
                        ->visible(fn($record) => $record->status === 'active'),
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
                        ->visible(fn($record) => $record->status === 'inactive'),
                    Tables\Actions\EditAction::make()->hiddenLabel(),
                    Tables\Actions\DeleteAction::make()->hiddenLabel(),
                    Tables\Actions\RestoreAction::make()
                ])
            ])->recordUrl(fn($record): string => route('filament.admin.resources.users.view', $record->id))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
