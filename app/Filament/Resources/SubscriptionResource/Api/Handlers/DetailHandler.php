<?php

namespace App\Filament\Resources\SubscriptionResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\SubscriptionResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\SubscriptionResource\Api\Transformers\SubscriptionTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = SubscriptionResource::class;


    /**
     * Show Subscription
     *
     * @param Request $request
     * @return SubscriptionTransformer
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

        return new SubscriptionTransformer($query);
    }
}
