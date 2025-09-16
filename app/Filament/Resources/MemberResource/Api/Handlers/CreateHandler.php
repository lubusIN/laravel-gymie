<?php
namespace App\Filament\Resources\MemberResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\MemberResource;
use App\Filament\Resources\MemberResource\Api\Requests\CreateMemberRequest;

class CreateHandler extends Handlers {
    public static string | null $uri = '/';
    public static string | null $resource = MemberResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel() {
        return static::$resource::getModel();
    }

    /**
     * Create Member
     *
     * @param CreateMemberRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handler(CreateMemberRequest $request)
    {
        $model = new (static::getModel());

        $model->fill($request->all());

        $model->save();

        return static::sendSuccessResponse($model, "Successfully Create Resource");
    }
}