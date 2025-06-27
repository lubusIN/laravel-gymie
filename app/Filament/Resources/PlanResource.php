<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Helpers\Helpers;
use App\Models\Plan;
use App\Models\Service;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    /**
     * Define the form schema for the resource.
     *
     * @param \Filament\Forms\Form $form
     * @return \Filament\Forms\Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema(Plan::getForm());
    }

    /**
     * Get the Filament table columns for the plan list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->emptyStateIcon(!Service::exists() ? 'heroicon-o-cog-8-tooth' : 'heroicon-o-pencil-square')
            ->emptyStateHeading(!Service::exists() ? 'No Services' : 'No Plans')
            ->emptyStateDescription(!Service::exists() ? 'Create a service to get started.' : 'Create a plan to get started.')
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label('New service')
                    ->url(fn() => route('filament.admin.resources.services.create'))
                    ->icon('heroicon-o-plus')
                    ->hidden(fn() => Service::exists()),
                Tables\Actions\CreateAction::make()
                    ->icon('heroicon-o-plus')
                    ->label('New plan')
                    ->visible(fn() => Service::exists()),
            ])
            ->columns(Plan::getTableColumns())
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
                            ->action(fn(Plan $record) => tap($record, function ($record) {
                                $record->update(['status' => 'active']);
                                Notification::make()
                                    ->title('Plan has been activated')
                                    ->success()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status === 'inactive'),
                        Tables\Actions\Action::make('mark_as_inactive')
                            ->color('danger')
                            ->label('Mark as inactive')
                            ->requiresConfirmation()
                            ->action(fn(Plan $record) => tap($record, function ($record) {
                                $record->update(['status' => 'inactive']);
                                Notification::make()
                                    ->title('Plan has been deactivated')
                                    ->danger()
                                    ->send();
                            }))
                            ->visible(fn($record) => $record->status === 'active'),
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
            ])->recordUrl(fn($record): string => route('filament.admin.resources.plans.view', $record->id))
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
                        TextEntry::make('code')
                            ->label('Code'),
                        TextEntry::make('name')
                            ->label('Name'),
                        TextEntry::make('description')
                            ->label('Description'),
                        TextEntry::make('service.name')
                            ->label('Service'),
                        TextEntry::make('days')
                            ->label('Days'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(Helpers::getCurrencyCode()),
                        TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->color(fn($state) => $state === 'active' ? 'success' : 'danger')
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                                default => ucfirst($state), // Fallback for any unexpected status
                            }),
                    ])->columns(3),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
            'view' => Pages\ViewPlan::route('/{record}')
        ];
    }
}
