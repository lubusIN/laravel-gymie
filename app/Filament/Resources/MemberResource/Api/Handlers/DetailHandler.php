<?php

namespace App\Filament\Resources\MemberResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\MemberResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\MemberResource\Api\Transformers\MemberTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = MemberResource::class;


    /**
     * Show Member
     *
     * @param Request $request
     * @return MemberTransformer
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

        return new MemberTransformer($query);
    }
}
