<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class UserInfolist
{
    /**
     * Configure the user "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->heading(function (User $record): HtmlString {
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
                        return new HtmlString("Details " . $html);
                    })
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->defaultImageUrl(fn(User $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name)
                            ->size(180)
                            ->circular()
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                TextEntry::make('name'),
                                TextEntry::make('email'),
                                TextEntry::make('contact'),
                                TextEntry::make('gender'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date()
                                    ->placeholder('N/A'),
                                TextEntry::make('roles.name')
                                    ->formatStateUsing(
                                        fn($state): string =>
                                        Str::headline($state)
                                    )
                                    ->badge(),
                            ])->columnSpan(4)->columns(3),
                    ])->columns(5),
                Section::make('Location')
                    ->schema([
                        TextEntry::make('address')->label('Address'),
                        Group::make()
                            ->schema([
                                TextEntry::make('country')->label('Country'),
                                TextEntry::make('state')
                                    ->placeholder('N/A'),
                                TextEntry::make('city')
                                    ->placeholder('N/A'),
                                TextEntry::make('pincode')->label('PIN Code'),
                            ])
                            ->columns(4),
                    ]),

            ]);
    }
}
