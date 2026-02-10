<?php

namespace App\Filament\Resources\Members\Schemas;

use App\Models\Member;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class MemberInfolist
{
    /**
     * Configure the member "view" infolist schema.
     */
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->heading(function (Member $record): HtmlString {
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
                        ImageEntry::make('photo')
                            ->hiddenLabel()
                            ->defaultImageUrl(fn(Member $record): ?string => 'https://ui-avatars.com/api/?background=000&color=fff&name=' . $record->name)
                            ->size(180)
                            ->circular()
                            ->columnSpan(1),
                        Group::make()
                            ->schema([
                                TextEntry::make('code')
                                    ->label('Member Code'),
                                TextEntry::make('name'),
                                TextEntry::make('gender')->label('Gender'),
                                TextEntry::make('email'),
                                TextEntry::make('contact'),
                                TextEntry::make('emergency_contact')->placeholder('N/A'),
                                TextEntry::make('dob')
                                    ->label('Date of Birth')
                                    ->date('d-m-Y'),
                                TextEntry::make('source')
                                    ->label('Source')
                                    ->placeholder('N/A'),
                                TextEntry::make('goal')
                                    ->label('Goal ?')
                                    ->placeholder('N/A'),
                                TextEntry::make('health_issue')
                                    ->label('Health Issue')
                                    ->placeholder('N/A'),
                            ])->columnSpan(4)->columns(3),
                    ])->columns(5),
                Section::make('Location')
                    ->columns(3)
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
                            ->columnSpan(2)
                            ->columns(4),
                    ]),

            ]);
    }
}
