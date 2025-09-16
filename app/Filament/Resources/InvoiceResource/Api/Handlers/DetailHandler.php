<?php

namespace App\Filament\Resources\InvoiceResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\InvoiceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\InvoiceResource\Api\Transformers\InvoiceTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = InvoiceResource::class;


    /**
     * Show Invoice
     *
     * @param Request $request
     * @return InvoiceTransformer
     */
    public function handler(Request $request)
    {
        $id = $request->route('id');
        
        $query = static::getEloquentQuery();

        $query = QueryBuilder::for(
            $query->where(static::getKeyName(), $id)
        )
            ->first();

        if (!$query) return static::sendNotFoundResponse();

        return new InvoiceTransformer($query);
    }
}
