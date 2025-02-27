<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'status' => $this->status,
            'custom_id' => $this->custom_id,
            'created_at' => $this->created_at,
            'asset_count' => $this->asset_count,
            'custom_field_values' => $this->whenLoaded('customFieldValues'),
            'additional_files' => $this->whenLoaded('additionalFiles'),
            'assets' => AssetResource::collection($this->whenLoaded('assets'))
        ];
    }
}
