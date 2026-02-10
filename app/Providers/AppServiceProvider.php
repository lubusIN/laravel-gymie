<?php

namespace App\Providers;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Illuminate\Support\Collection;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Command as ArtisanCommand;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make('gymie-styles', __DIR__ . '/../../resources/css/custom.css'),
        ]);

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
            $column->toggleable(isToggledHiddenByDefault: false);
        });

        $this->configureDeletionPrevention();
    }

    /**
     * Prevent deletion of records that still have related data.
     */
    protected function configureDeletionPrevention(): void
    {
        $map = config('prevent-deletion', []);
        DeleteAction::configureUsing(function (DeleteAction $action) use ($map): DeleteAction {
            return $action
                ->requiresConfirmation(function (Action $action, $record) use ($map) {
                    $class = get_class($record);
                    $action->modalIcon('heroicon-o-trash');
                    if (isset($map[$class])) {
                        foreach ($map[$class] as $relation) {
                            if ($record->$relation()->exists()) {
                                $count = $record->$relation()->count();
                                $moduleName = class_basename($record);
                                $label = Str::kebab($relation);
                                $action
                                    ->modalIcon('heroicon-o-x-mark',)
                                    ->modalHeading("Oops! Cannot Delete {$moduleName}")
                                    ->modalDescription("This record has {$count} {$label}. Please delete them first.")
                                    ->modalCancelAction(false)
                                    ->modalSubmitAction(false);
                                break;
                            }
                        }
                    }

                    return $action;
                });
        }, isImportant: true);

        DeleteBulkAction::configureUsing(function (DeleteBulkAction $action) use ($map): DeleteBulkAction {
            return $action
                ->requiresConfirmation(function (DeleteBulkAction $action, Collection $records) use ($map) {
                    foreach ($records as $record) {
                        $class = get_class($record);
                        $action->modalIcon('heroicon-o-trash');
                        if (isset($map[$class])) {
                            foreach ($map[$class] as $relation) {
                                if ($record->$relation()->exists()) {
                                    $count      = $record->$relation()->count();
                                    $moduleName = Str::pluralStudly(class_basename($record));
                                    $label = Str::kebab($relation);
                                    $action
                                        ->modalIcon('heroicon-o-x-mark')
                                        ->modalHeading("Oops! Cannot Delete {$moduleName}")
                                        ->modalDescription("At least one selected {$moduleName} has {$count} {$label}. Please delete those first.")
                                        ->modalCancelAction(false)
                                        ->modalSubmitAction(false);
                                    break 2;
                                }
                            }
                        }
                    }

                    return $action;
                });
        }, isImportant: true);
    }
}
