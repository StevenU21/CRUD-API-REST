<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->when($request->has('include_id'), $this->id),
            'slug' => $this->when($request->has('include_slug'), $this->slug),
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'created_at' => $this->when($request->has('include_timestamps'), $this->created_at->format($request->get('date_format', 'Y-m-d H:i:s')) ),
        ];
    }
}
