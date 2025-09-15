<?php
namespace App\Filament\Resources\EnquiryResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\EnquiryResource;
use App\Filament\Resources\EnquiryResource\Api\Requests\CreateEnquiryRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = EnquiryResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Enquiry
     *
     * @param CreateEnquiryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateEnquiryRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}