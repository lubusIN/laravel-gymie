<?php
namespace App\Filament\Resources\EnquiryResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Enquiry;

/**
 * @property Enquiry $resource
 */
class EnquiryTransformer extends JsonResource
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
