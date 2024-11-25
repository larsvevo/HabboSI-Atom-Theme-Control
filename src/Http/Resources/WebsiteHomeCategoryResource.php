<?php

namespace Atom\Theme\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebsiteHomeCategoryResource extends JsonResource
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
            'children' => WebsiteHomeCategoryResource::collection($this->whenLoaded('children')),
        ];
    }
}
