<?php

namespace App\Filament\Resources\Subscriptions\Schemas;

use App\Models\Subscription;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class SubscriptionInfolist
{
    /**
     * Configure the subscription "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->heading(function (Subscription $record): HtmlString {
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
                        return new HtmlString('Details ' . $html);
                    })
                    ->schema([
                        TextEntry::make('member')
                            ->label('Member')
                            ->columnSpan(2)
                            ->formatStateUsing(fn($record): string => "{$record->member->code} – {$record->member->name}"),
                        TextEntry::make('plan')
                            ->label('Plan')
                            ->columnSpan(2)
                            ->formatStateUsing(fn($record): string => "{$record->plan->code} – {$record->plan->name}"),
                        TextEntry::make('start_date')
                            ->label('Start Date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->label('End Date')
                            ->date(),
                    ])->columns(6),
            ]);
    }
}
