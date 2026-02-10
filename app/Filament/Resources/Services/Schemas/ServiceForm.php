<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class ServiceForm
{
    /**
     * Configure the service form schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->label('Name')
                    ->placeholder('Service name')
                    ->required(),
                Textarea::make('description')
                    ->placeholder('Brief description of the service')
                    ->label('Description')
                    ->required()
            ]);
    }
}
