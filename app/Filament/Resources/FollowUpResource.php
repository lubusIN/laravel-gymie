<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FollowUpResource\Pages;
use App\Filament\Resources\FollowUpResource\RelationManagers;
use App\Models\FollowUp;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FollowUpResource extends Resource
{
    protected static ?string $model = FollowUp::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('enquiry_id')
                    ->label('Enquiry')
                    ->relationship(name: 'enquiry', titleAttribute: 'name')
                    ->searchable()
                    ->placeholder('')
                    ->required()
                    ->preload(),
                DatePicker::make('follow_up_date')
                    ->native(false)
                    ->label('Date')
                    ->displayFormat('d/m/Y')
                    ->closeOnDateSelection()
                    ->placeholder('dd/mm/yyyy')
                    ->suffixIcon('heroicon-m-calendar-days')
                    ->minDate(now()),
                Select::make('follow_up_method')
                    ->options([
                        'call' => 'Call',
                        'email' => 'Email',
                        'in_person' => 'In person',
                        'whatsapp' => 'WhatsApp',
                        'others' => 'Others'
                    ])->default('call')
                    ->label('Follow-up method')
                    ->searchable(),
                Textarea::make('outcome')
                    ->placeholder('Not interested, etc.')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('enquiry.name')
                    ->searchable()
                    ->label('Enquiry')
                    ->sortable(),
                TextColumn::make('follow_up_date')
                    ->searchable()
                    ->label('Date')
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('follow_up_method')
                    ->label('Method')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'done' => 'heroicon-o-check-circle',
                        'pending' => 'heroicon-o-x-circle',
                    })
                    ->iconColor(fn(string $state): string => match ($state) {
                        'done' => 'success',
                        'pending' => 'warning',
                    })
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('outcome')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\Filter::make('date')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()->hiddenLabel(),
                    Tables\Actions\DeleteAction::make()->hiddenLabel(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFollowUps::route('/'),
            'create' => Pages\CreateFollowUp::route('/create'),
            'edit' => Pages\EditFollowUp::route('/{record}/edit'),
        ];
    }
}
