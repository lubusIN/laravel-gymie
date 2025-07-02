<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Enums\Status;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-m-plus'),
        ];
    }

    public function getBreadcrumbs(): array
    {
        return [
            'Administration',
            'Users',
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'active' => Tab::make('Active')
                ->badge(User::query()->where('status', 'active')->count())
                ->badgeColor(Status::Active->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'active')),
            'inactive' => Tab::make('Inactive')
                ->badge(User::query()->where('status', 'inactive')->count())
                ->badgeColor(Status::Inactive->getColor())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'inactive')),
        ];
    }
}
