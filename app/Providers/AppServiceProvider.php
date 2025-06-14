<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Configure the CreateAction globally to use a specific icon.
         */
        CreateAction::configureUsing(function (CreateAction $action) {
            $action->icon('heroicon-s-plus');
        });

        /**
         * Configure the EditAction and DeleteAction globally to use specific icons.
         */
        EditAction::configureUsing(function (EditAction $action) {
            $action->icon('heroicon-s-pencil-square');
        });

        /**
         * Configure the DeleteAction globally to use a specific icon.
         */
        DeleteAction::configureUsing(function (DeleteAction $action) {
            $action->icon('heroicon-s-trash');
        });

        /**
         * Configure the ViewAction globally to use a specific icon.
         */
        ViewAction::configureUsing(function (ViewAction $action) {
            $action->icon('heroicon-s-eye');
        });

        /**
         * Configure the Table component globally to set default sorting.
         */
        Table::configureUsing(function (Table $table) {
            $table->defaultSort('id', 'desc');
        });

        /**
         * Configure the Select component globally to be searchable, non-native, and preloaded.
         */
        Select::configureUsing(function (Select $select) {
            $select
                ->searchable()
                ->native(false)
                ->preload();
        });

        /**
         * Configure the DatePicker component globally to use a specific format and placeholder.
         */
        DatePicker::configureUsing(function (DatePicker $datePicker) {
            $datePicker
                ->native(false)
                ->placeholder('01-01-2001')
                ->displayFormat('d/m/Y')
                ->prefixIcon('heroicon-o-calendar-days');
        });

        /**
         * Configure the TextColumn globally to be toggleable and hidden by default.
         */
        TextColumn::configureUsing(function (TextColumn $column) {
            $column->toggleable(isToggledHiddenByDefault: true);
        });
    }
}
