<?php
namespace App\Filament\Resources\SubscriptionResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\SubscriptionResource;
use App\Filament\Resources\SubscriptionResource\Api\Requests\UpdateSubscriptionRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = SubscriptionResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Subscription
     *
     * @param UpdateSubscriptionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateSubscriptionRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}