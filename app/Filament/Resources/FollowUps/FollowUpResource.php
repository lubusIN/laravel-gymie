<?php

namespace App\Filament\Resources\FollowUps;

use App\Models\FollowUp;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use App\Filament\Resources\FollowUps\Tables\FollowUpTable;
use App\Filament\Resources\FollowUps\Pages\ListFollowUps;
use App\Filament\Resources\FollowUps\Schemas\FollowUpForm;
use App\Filament\Resources\FollowUps\Schemas\FollowUpInfolist;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    /**
     * Define the form schema for the resource.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return FollowUpForm::configure($schema);
    }

    /**
     * Get the Filament table columns for the follow-up list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return FollowUpTable::configure($table);
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return FollowUpInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFollowUps::route('/'),
        ];
    }
}
