<?php

namespace App\Filament\Resources\Enquiries;

use App\Models\Enquiry;
use Filament\Schemas\Schema;
use App\Filament\Resources\Enquiries\Pages\ListEnquiries;
use App\Filament\Resources\Enquiries\Pages\CreateEnquiry;
use App\Filament\Resources\Enquiries\Pages\EditEnquiry;
use App\Filament\Resources\Enquiries\Pages\ViewEnquiry;
use App\Filament\Resources\Enquiries\RelationManagers\FollowUpsRelationManager;
use App\Filament\Resources\Enquiries\Schemas\EnquiryForm;
use App\Filament\Resources\Enquiries\Schemas\EnquiryInfolist;
use App\Filament\Resources\Enquiries\Tables\EnquiryTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EnquiryResource extends Resource
{
    protected static ?string $model = Enquiry::class;

    /**
     * Define the form schema for the resource.
     *
     * @param Schema $schema
     * @return Schema
     */
    public static function form(Schema $schema): Schema
    {
        return EnquiryForm::configure($schema);
    }

    /**
     * Get the Filament table columns for the enquiry list view.
     *
     * @return array
     */
    public static function table(Table $table): Table
    {
        return EnquiryTable::configure($table);
    }

    /**
     * Add infolist to the resource.
     * 
     * @param Schema $schema
     * @return Schema
     */
    public static function infolist(Schema $schema): Schema
    {
        return EnquiryInfolist::configure($schema);
    }

    /**
     * Define the relations for the resource.
     *
     * @return array
     */
    public static function getRelations(): array
    {
        return [
            FollowUpsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEnquiries::route('/'),
            'create' => CreateEnquiry::route('/create'),
            'edit'   => EditEnquiry::route('/{record}/edit'),
            'view'   => ViewEnquiry::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
