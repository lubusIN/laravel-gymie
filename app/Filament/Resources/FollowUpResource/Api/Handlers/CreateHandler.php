<?php
namespace App\Filament\Resources\FollowUpResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\FollowUpResource;
use App\Filament\Resources\FollowUpResource\Api\Requests\CreateFollowUpRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = FollowUpResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create FollowUp
     *
     * @param CreateFollowUpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateFollowUpRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}