<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CltLayupResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'supplier'    => new SupplierResource($this->whenLoaded('supplier')),
            'layers_count'=> $this->whenCounted('layers'),
        ];
    }
}
