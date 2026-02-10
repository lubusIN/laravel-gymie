<?php

namespace App\Filament\Resources\FollowUps\Schemas;

use App\Models\FollowUp;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class FollowUpInfolist
{
    /**
     * Configure the follow-up "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->schema([
                Fieldset::make('')
                    ->label(function (FollowUp $record): HtmlString {
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
                        TextEntry::make('enquiry.name')
                            ->label('Enquiry')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->url(fn($record): string => route('filament.admin.resources.enquiries.view', $record->enquiry_id)),
                        TextEntry::make('user.name')
                            ->label('Handled By')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->placeholder('N/A')
                            ->url(
                                fn($record) => $record->user_id
                                    ? route('filament.admin.resources.users.view', $record->user_id)
                                    : null
                            ),
                        TextEntry::make('method')
                            ->label('Method'),
                        TextEntry::make('schedule_date')
                            ->label('Schedule Date')
                            ->date('d-m-Y'),
                        TextEntry::make('outcome')
                            ->label('Outcome')
                            ->placeholder('N/A')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}
