<?php
namespace App\Filament\Resources\SubscriptionResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Subscription;

/**
 * @property Subscription $resource
 */
class SubscriptionTransformer extends JsonResource
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
