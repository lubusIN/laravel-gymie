<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Enums\Status;
use App\Helpers\Helpers;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;

class PlanForm
{
    /**
     * Configure the plan form schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Fieldset::make()
                    ->label(function (Get $get): HtmlString {
                        $rawStatus = $get('status');
                        $status = Status::tryFrom($rawStatus) ?? Status::Active;
                        $html = Blade::render(
                            '<x-filament::badge class="inline-flex ml-2" :color="$color">
                                {{ $label }}
                            </x-filament::badge>',
                            [
                                'color' => $status->getColor(),
                                'label' => $status->getLabel(),
                            ]
                        );
                        return new HtmlString($html);
                    })
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->placeholder('Name of the plan')
                            ->unique(ignoreRecord: true,)
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('code')
                            ->placeholder('Code for the plan')
                            ->label('Code')
                            ->unique(ignoreRecord: true)
                            ->required(),
                        Select::make('service_id')
                            ->label('Service')
                            ->relationship(name: 'service', titleAttribute: 'name')
                            ->placeholder('Select service')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('days')
                            ->required()
                            ->placeholder('Number of days for the plan')
                            ->numeric()
                            ->label('Days')
                            ->columnSpan(1),
                        TextInput::make('amount')
                            ->placeholder('Enter amount of the plan')
                            ->numeric()
                            ->prefix(Helpers::getCurrencySymbol())
                            ->label('Amount')
                            ->required()
                            ->columnSpan(2),
                        TextInput::make('description')
                            ->placeholder('Brief description of the plan')
                            ->label('Description')
                            ->columnSpanFull()
                    ])->columns(3)
            ]);
    }
}
