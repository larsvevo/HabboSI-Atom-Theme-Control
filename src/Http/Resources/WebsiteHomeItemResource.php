<?php

namespace Atom\Theme\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteHomeItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'website_home_category_id' => $this->website_home_category_id,
            'description' => $this->description,
            'type' => $this->type,
            'image' => $this->image,
            'image_url' => $this->image_url,
            'count' => $this->count,
            'price' => $this->price,
            'currency_price' => $this->currency_price,
            'currency_type' => $this->currency_type,
            'data' => $this->data,
            'pivot' => $this->whenPivotLoaded('user_website_home_item', fn () => [
                'id' => $this->pivot->id,
                'left' => $this->pivot->left,
                'top' => $this->pivot->top,
                'z' => $this->pivot->z,
                'data' => (object) json_decode($this->pivot->data),
            ]),
        ];
    }
}
