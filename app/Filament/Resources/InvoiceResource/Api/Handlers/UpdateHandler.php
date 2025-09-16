<?php
namespace App\Filament\Resources\InvoiceResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\InvoiceResource;
use App\Filament\Resources\InvoiceResource\Api\Requests\UpdateInvoiceRequest;

class UpdateHandler extends Handlers {
    public static string | null $uri = '/{id}';
    public static string | null $resource = InvoiceResource::class;

    public static function getMethod()
    {
        return Handlers::PUT;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }


    /**
     * Update Invoice
     *
     * @param UpdateInvoiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(UpdateInvoiceRequest $request)
    {
        $id = $request->route('id');

        $model = static::getModel()::find($id);

        if (!$model) return static::sendNotFoundResponse();

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Update Resource");
    }
}