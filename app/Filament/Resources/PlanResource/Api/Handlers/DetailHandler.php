<?php

namespace App\Filament\Resources\PlanResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\PlanResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\PlanResource\Api\Transformers\PlanTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = PlanResource::class;


    /**
     * Show Plan
     *
     * @param Request $request
     * @return PlanTransformer
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

        return new PlanTransformer($query);
    }
}
