<?php
namespace App\Filament\Resources\MemberResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\MemberResource;
use Illuminate\Routing\Router;


class MemberApiService extends ApiService
{
    protected static string | null $resource = MemberResource::class;

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
