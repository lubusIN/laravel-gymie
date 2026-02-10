<?php

namespace App\Filament\Resources\FollowUps\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Filament\Resources\Enquiries\RelationManagers\FollowUpsRelationManager;

class FollowUpForm
{
    /**
     * Configure the follow-up form schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Select::make('enquiry_id')
                    ->label('Enquiry')
                    ->relationship(name: 'enquiry', titleAttribute: 'name')
                    ->placeholder('Select Enquiry')
                    ->hiddenOn(FollowUpsRelationManager::class)
                    ->required(),
                Select::make('method')
                    ->options([
                        'call' => 'Call',
                        'email' => 'Email',
                        'in_person' => 'In person',
                        'whatsapp' => 'WhatsApp',
                        'other' => 'Others'
                    ])->default('call')
                    ->required()
                    ->label('Method'),
                DatePicker::make('schedule_date')
                    ->label('Schedule Date')
                    ->closeOnDateSelection()
                    ->required()
                    ->required()
                    ->minDate(now()),
            ]);
    }
}
