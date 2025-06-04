<?php

namespace App\Models;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the Filament form schema for the services.
     *
     * @return array
     */
    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Name')
                ->placeholder('Service name')
                ->required(),
            TextInput::make('description')
                ->placeholder('Brief description of the service')
                ->label('Description')
                ->required(),
        ];
    }

    /**
     * Get the Filament table columns for the services list view.
     *
     * @return array
     */
    public static function getTableColumns(): array
    {
        return [
            TextColumn::make('id')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('name')
                ->searchable()
                ->label('Name')
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('description')
                ->searchable()
                ->label('Description')
                ->toggleable(isToggledHiddenByDefault: false),
            TextColumn::make('created_at')
                ->searchable()
                ->date('d-m-Y')
                ->toggleable(isToggledHiddenByDefault: true),
        ];
    }

}
