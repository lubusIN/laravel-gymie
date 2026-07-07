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
                        $status = $record->status ?? \App\Enums\Status::Inactive;
                        $html = Blade::render(
                            '<x-filament::badge class="inline-flex ml-2" :color="$color">
                                {{ $label }}
                            </x-filament::badge>',
                            [
                                'color' => $status->getColor(),
                                'label' => $status->getLabel(),
                            ]
                        );

                        return new HtmlString(e(__('app.ui.details')).' '.$html);
                    })
                    ->schema([
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->defaultImageUrl(fn (User $record): string => 'https://ui-avatars.com/api/?background=000&color=fff&name='.$record->name)
                            ->size(180)
                            ->circular()
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                TextEntry::make('name')->label(__('app.fields.name')),
                                TextEntry::make('email')->label(__('app.fields.email')),
                                TextEntry::make('contact')->label(__('app.fields.contact')),
                                TextEntry::make('gender')->label(__('app.fields.gender')),
                                TextEntry::make('dob')
                                    ->label(__('app.fields.dob'))
                                    ->date()
                                    ->placeholder(__('app.placeholders.na')),
                                TextEntry::make('roles.name')
                                    ->label(__('app.fields.role'))
                                    ->formatStateUsing(
                                        fn ($state): string => Str::headline($state)
                                    )
                                    ->badge(),
                            ])->columnSpan(4)->columns(3),
                    ])->columns(5),
                Section::make(__('app.ui.location'))
                    ->schema([
                        TextEntry::make('address')->label(__('app.fields.address')),
                        Group::make()
                            ->schema([
                                TextEntry::make('country')->label(__('app.fields.country')),
                                TextEntry::make('state')
                                    ->label(__('app.fields.state'))
                                    ->placeholder(__('app.placeholders.na')),
                                TextEntry::make('city')
                                    ->label(__('app.fields.city'))
                                    ->placeholder(__('app.placeholders.na')),
                                TextEntry::make('pincode')
                                    ->label(__('app.fields.pincode')),
                            ])
                            ->columns(4),
                    ]),

            ]);
    }
}
