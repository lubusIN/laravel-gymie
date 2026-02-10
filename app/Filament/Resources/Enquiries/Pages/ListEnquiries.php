<?php

namespace App\Filament\Resources\Enquiries\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Status;
use App\Filament\Resources\Enquiries\EnquiryResource;
use App\Models\Enquiry;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEnquiries extends ListRecords
{
    protected static string $resource = EnquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-m-plus')
                ->hidden(!Enquiry::exists()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Sales',
            'Enquiries',
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'lead' => Tab::make('Lead')
                ->badge(Enquiry::query()->where('status', 'lead')->count())
                ->badgeColor(Status::Lead->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'lead')),
            'member' => Tab::make('Member')
                ->badge(Enquiry::query()->where('status', 'member')->count())
                ->badgeColor(Status::Member->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'member')),
            'lost' => Tab::make('Lost')
                ->badge(Enquiry::query()->where('status', 'lost')->count())
                ->badgeColor(Status::Lost->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'lost')),
        ];
    }
}
