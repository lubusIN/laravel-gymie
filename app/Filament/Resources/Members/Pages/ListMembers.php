<?php

namespace App\Filament\Resources\Members\Pages;

use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use App\Enums\Status;
use App\Filament\Resources\Members\MemberResource;
use App\Models\Member;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMembers extends ListRecords
{
    protected static string $resource = MemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->icon('heroicon-m-plus')
                ->hidden(!Member::exists()),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'active' => Tab::make('Active')
                ->badge(Member::query()->where('status', 'active')->count())
                ->badgeColor(Status::Active->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),
            'inactive' => Tab::make('Inactive')
                ->badge(Member::query()->where('status', 'inactive')->count())
                ->badgeColor(Status::Inactive->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive')),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Memberships',
            MemberResource::getUrl('index')   => 'Members',
        ];
    }
}
