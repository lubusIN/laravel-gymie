<?php

namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\ServiceResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\ServiceResource\Api\Transformers\ServiceTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = ServiceResource::class;


    /**
     * Show Service
     *
     * @param Request $request
     * @return ServiceTransformer
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

        return new ServiceTransformer($query);
    }
}
