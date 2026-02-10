<?php

namespace App\Filament\Resources\Services;

use Filament\Schemas\Schema;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Resources\Services\Schemas\ServiceForm;
use App\Filament\Resources\Services\Schemas\ServiceInfolist;
use App\Filament\Resources\Services\Tables\ServiceTable;
use App\Models\Service;
use Filament\Resources\Resource;
use Filament\Tables\Table;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    public static function form(Schema $schema): Schema
    {
        return ServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ServiceTable::configure($table);
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return ServiceInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListServices::route('/'),
        ];
    }
}
