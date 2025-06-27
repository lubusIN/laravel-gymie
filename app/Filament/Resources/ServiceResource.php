<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(Service::getForm())
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->emptyStateIcon('heroicon-o-cog-8-tooth')
            ->emptyStateHeading('No Services')
            ->emptyStateDescription('Create a service to get started.')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New service')
                    ->modalHeading('New service')
                    ->modalWidth('sm')
                    ->createAnother(false)
                    ->hidden(fn() => Service::exists()),
            ])
            ->columns(Service::getTableColumns())
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalCancelAction(false)
                    ->modalWidth('sm'),
                Tables\Actions\EditAction::make()->modalWidth('sm'),
                Tables\Actions\DeleteAction::make(),
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
                Section::make('')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('description')
                            ->label('Description')
                    ])->columns(1)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
        ];
    }
}
