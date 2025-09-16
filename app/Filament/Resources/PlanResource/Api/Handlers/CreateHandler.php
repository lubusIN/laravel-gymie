<?php
namespace App\Filament\Resources\PlanResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\PlanResource;
use App\Filament\Resources\PlanResource\Api\Requests\CreatePlanRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = PlanResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Plan
     *
     * @param CreatePlanRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreatePlanRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}