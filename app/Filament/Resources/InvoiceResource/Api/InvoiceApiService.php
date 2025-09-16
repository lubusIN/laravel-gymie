<?php
namespace App\Filament\Resources\InvoiceResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\InvoiceResource;
use Illuminate\Routing\Router;


class InvoiceApiService extends ApiService
{
    protected static string | null $resource = InvoiceResource::class;

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
