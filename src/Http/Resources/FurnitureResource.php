<?php

namespace Atom\Theme\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FurnitureResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->itemBase->id,
            'key' => $this->itemBase->item_name,
            'name' => $this->itemBase->furnitureData->name,
            'description' => $this->itemBase->furnitureData->description,
            'url' => Storage::disk('furniture_icons')->url(sprintf('%s_icon.png', str_replace('*', '_', $this->itemBase->item_name))),
            'cost_credits' => $this->cost_credits,
            'in_circulation' => $this->itemBase->items
                ->filter(fn ($item) => optional($item->user)->rank < 4)
                ->count(),
        ];
    }
}
