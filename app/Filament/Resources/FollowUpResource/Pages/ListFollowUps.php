<?php

namespace App\Filament\Resources\FollowUpResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\FollowUpResource;
use App\Models\Enquiry;
use App\Models\FollowUp;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListFollowUps extends ListRecords
{
    protected static string $resource = FollowUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus')
                ->modalWidth('sm')
                ->modalHeading('New follow up')
                ->createAnother(false)
                ->hidden(!Enquiry::exists() || !FollowUp::exists()),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Sales',
            'Follow Ups',
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->badge(FollowUp::query()->where('status', 'pending')->count())
                ->badgeColor(Status::Pending->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'done' => Tab::make('Done')
                ->badge(FollowUp::query()->where('status', 'done')->count())
                ->badgeColor(Status::Done->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'done')),
        ];
    }
}
