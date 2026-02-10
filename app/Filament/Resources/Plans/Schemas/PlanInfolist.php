<?php

namespace App\Filament\Resources\Plans\Schemas;

use App\Helpers\Helpers;
use App\Models\Plan;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class PlanInfolist
{
    /**
     * Configure the plan "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Fieldset::make('')
                    ->label(function (Plan $record): HtmlString {
                        $status = $record->status;
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
                        TextEntry::make('code')
                            ->label('Code')
                            ->columnSpan(1),
                        TextEntry::make('name')
                            ->label('Name')
                            ->columnSpan(2),
                        TextEntry::make('service.name')
                            ->label('Service'),
                        TextEntry::make('days')
                            ->label('Days'),
                        TextEntry::make('amount')
                            ->label('Amount')
                            ->money(Helpers::getCurrencyCode()),
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }
}
