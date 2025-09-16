<?php
namespace App\Filament\Resources\FollowUpResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\FollowUp;

/**
 * @property FollowUp $resource
 */
class FollowUpTransformer extends JsonResource
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
