<?php

namespace App\Filament\Resources\Plans;

use App\Models\Plan;
use Filament\Schemas\Schema;
use App\Filament\Resources\Plans\Pages\ListPlans;
use App\Filament\Resources\Plans\Schemas\PlanForm;
use App\Filament\Resources\Plans\Schemas\PlanInfolist;
use App\Filament\Resources\Plans\Tables\PlanTable;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    /**
     * Define the form schema for the resource.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return PlanForm::configure($schema);
    }

    /**
     * Get the Filament table columns for the plan list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return  PlanTable::configure($table);
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return PlanInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPlans::route('/'),
        ];
    }
}
