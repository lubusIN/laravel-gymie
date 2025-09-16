<?php
namespace App\Filament\Resources\MemberResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Member;

/**
 * @property Member $resource
 */
class MemberTransformer extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->toArray();
    }
}
