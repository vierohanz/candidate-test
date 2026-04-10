<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CltLayerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'layup_id'    => $this->layup_id,
            'layer_order' => $this->layer_order,
            'thickness'   => (float) $this->thickness,
            'width'       => (float) $this->width,
            'angle'       => (float) $this->angle,
            'layup'       => new CltLayupResource($this->whenLoaded('layup')),
        ];
    }
}
