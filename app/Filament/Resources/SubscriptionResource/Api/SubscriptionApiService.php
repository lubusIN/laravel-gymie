<?php
namespace App\Filament\Resources\SubscriptionResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\SubscriptionResource;
use Illuminate\Routing\Router;


class SubscriptionApiService extends ApiService
{
    protected static string | null $resource = SubscriptionResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
