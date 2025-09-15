<?php

namespace App\Filament\Resources\FollowUpResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\FollowUpResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\FollowUpResource\Api\Transformers\FollowUpTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = FollowUpResource::class;


    /**
     * Show FollowUp
     *
     * @param Request $request
     * @return FollowUpTransformer
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

        return new FollowUpTransformer($query);
    }
}
