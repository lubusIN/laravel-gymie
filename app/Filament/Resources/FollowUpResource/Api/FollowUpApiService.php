<?php
namespace App\Filament\Resources\FollowUpResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\FollowUpResource;
use Illuminate\Routing\Router;


class FollowUpApiService extends ApiService
{
    protected static string | null $resource = FollowUpResource::class;

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
