<?php
namespace App\Filament\Resources\ServiceResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\ServiceResource;
use Illuminate\Routing\Router;


class ServiceApiService extends ApiService
{
    protected static string | null $resource = ServiceResource::class;

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
