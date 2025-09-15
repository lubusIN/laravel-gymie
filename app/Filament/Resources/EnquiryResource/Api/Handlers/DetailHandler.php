<?php

namespace App\Filament\Resources\EnquiryResource\Api\Handlers;

use App\Filament\Resources\SettingResource;
use App\Filament\Resources\EnquiryResource;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use App\Filament\Resources\EnquiryResource\Api\Transformers\EnquiryTransformer;

class DetailHandler extends Handlers
{
    public static string | null $uri = '/{id}';
    public static string | null $resource = EnquiryResource::class;


    /**
     * Show Enquiry
     *
     * @param Request $request
     * @return EnquiryTransformer
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

        return new EnquiryTransformer($query);
    }
}
