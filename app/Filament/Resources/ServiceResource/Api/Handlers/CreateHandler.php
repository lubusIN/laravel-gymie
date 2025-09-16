<?php
namespace App\Filament\Resources\ServiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\ServiceResource\Api\Requests\CreateServiceRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = ServiceResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Service
     *
     * @param CreateServiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateServiceRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}