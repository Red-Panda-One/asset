<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->value,
            'image' => $this->image,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
            'custom_id' => $this->custom_id,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'custom_field_values' => $this->whenLoaded('customFieldValues'),
            'additional_files' => $this->whenLoaded('additionalFiles')
        ];
    }
}
