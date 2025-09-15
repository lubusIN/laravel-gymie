<?php
namespace App\Filament\Resources\InvoiceResource\Api\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Invoice;

/**
 * @property Invoice $resource
 */
class InvoiceTransformer extends JsonResource
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
