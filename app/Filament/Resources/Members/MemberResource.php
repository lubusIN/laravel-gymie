<?php

namespace App\Filament\Resources\Members;

use Filament\Schemas\Schema;
use App\Filament\Resources\Members\Pages\ListMembers;
use App\Filament\Resources\Members\Pages\CreateMember;
use App\Filament\Resources\Members\Pages\EditMember;
use App\Filament\Resources\Members\Pages\ViewMember;
use App\Filament\Resources\Members\RelationManagers\SubscriptionsRelationManager;
use App\Filament\Resources\Members\Schemas\MemberForm;
use App\Filament\Resources\Members\Schemas\MemberInfolist;
use App\Filament\Resources\Members\Tables\MemberTable;
use App\Models\Member;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MemberResource extends Resource
{
    protected static ?string $model = Member::class;

    /**
     * Define the form schema for the resource.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return MemberForm::configure($schema);
    }

    /**
     * Get the Filament table columns for the members list view.
     *
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return MemberTable::configure($table);
    }

    /**
     * Add infolist to the resource.
     */
    public static function infolist(Schema $schema): Schema
    {
        return MemberInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMembers::route('/'),
            'create' => CreateMember::route('/create'),
            'edit' => EditMember::route('/{record}/edit'),
            'view' => ViewMember::route('/{record}')
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
