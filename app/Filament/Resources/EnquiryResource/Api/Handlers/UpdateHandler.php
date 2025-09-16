<?php
namespace App\Filament\Resources\EnquiryResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\EnquiryResource;
use App\Filament\Resources\EnquiryResource\Api\Requests\UpdateEnquiryRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = EnquiryResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Enquiry
     *
     * @param UpdateEnquiryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateEnquiryRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}