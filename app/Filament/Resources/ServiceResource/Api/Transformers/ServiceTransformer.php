<?php
namespace App\Filament\Resources\ServiceResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Service;

/**
 * @property Service $resource
 */
class ServiceTransformer extends JsonResource
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
