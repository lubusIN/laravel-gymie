<?php

namespace App\Filament\Resources\UserResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\UserResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\UserResource\Api\Transformers\UserTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = UserResource::class;


    /**
     * Show User
     *
     * @param Request $request
     * @return UserTransformer
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

        return new UserTransformer($query);
    }
}
