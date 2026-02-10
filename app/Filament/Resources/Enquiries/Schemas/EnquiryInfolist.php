<?php

namespace App\Filament\Resources\Enquiries\Schemas;

use App\Models\Enquiry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class EnquiryInfolist
{
    /**
     * Configure the enquiry infolist schema.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(4)
            ->schema([
                Section::make('Details')
                    ->heading(function (Enquiry $record): HtmlString {
                        $status = $record->status;
                        $html = Blade::render(
                            '<x-filament::badge class="inline-flex" style="margin-left:6px;" :color="$color">
                                        {{ $label }}
                                    </x-filament::badge>',
                            ['color' => $status->getColor(), 'label' => $status->getLabel(),]
                        );
                        return new HtmlString('Details ' . $html);
                    })
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')->label('Email')->copyable(),
                        TextEntry::make('contact')->label('Contact')->copyable(),
                        TextEntry::make('gender')->label('Gender'),
                        TextEntry::make('dob')
                            ->label('Date of Birth')
                            ->date(),
                        TextEntry::make('date')
                            ->date(),
                        TextEntry::make('user.name')
                            ->label('Lead Owner')
                            ->weight(FontWeight::Bold)
                            ->color('success')
                            ->url(fn($record): string => route('filament.admin.resources.users.view', $record->user_id)),
                        TextEntry::make('start_by')
                            ->label('Preferred Start Date')
                            ->date()
                            ->placeholder('N/A'),
                    ])
                    ->columns(3)
                    ->columnSpan(4),
                Grid::make()
                    ->columnSpan(4)
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'xl'      => 5,
                    ])
                    ->schema([
                        Section::make('Location')
                            ->columnSpan([
                                'default' => 4,
                                'sm'      => 1,
                                'xl'      => 3,
                            ])
                            ->schema([
                                TextEntry::make('address')->label('Address'),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('country')->label('Country'),
                                        TextEntry::make('state')
                                            ->label('State')
                                            ->placeholder('N/A'),
                                        TextEntry::make('city')
                                            ->label('City')
                                            ->placeholder('N/A'),
                                        TextEntry::make('pincode')->label('PIN Code'),
                                    ])
                                    ->columns(4),
                            ]),
                        Section::make('Preferences')
                            ->columnSpan([
                                'default' => 4,
                                'sm'      => 1,
                                'xl'      => 2,
                            ])
                            ->columns([
                                'sm' => 1,
                                'md' => 2,
                            ])
                            ->schema([
                                TextEntry::make('interested_in')
                                    ->label('Interested In')
                                    ->columnSpanFull()
                                    ->placeholder('N/A'),
                                TextEntry::make('source')
                                    ->label('Source'),
                                TextEntry::make('goal')
                                    ->label('Goal ?'),
                            ]),
                    ])
            ]);
    }
}
