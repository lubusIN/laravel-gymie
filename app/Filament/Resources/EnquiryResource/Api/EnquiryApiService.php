<?php
namespace App\Filament\Resources\EnquiryResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\EnquiryResource;
use Illuminate\Routing\Router;


class EnquiryApiService extends ApiService
{
    protected static string | null $resource = EnquiryResource::class;

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
