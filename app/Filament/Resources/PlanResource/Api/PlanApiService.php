<?php
namespace App\Filament\Resources\PlanResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\PlanResource;
use Illuminate\Routing\Router;


class PlanApiService extends ApiService
{
    protected static string | null $resource = PlanResource::class;

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
