<?php

namespace Atom\Theme\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TradeLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $userOneItems = $this->items
            ->where('user_id', $this->user_one_id)
            ->pluck('item')
            ->filter();

        $userTwoItems = $this->items
            ->where('user_id', $this->user_two_id)
            ->pluck('item')
            ->filter();

        return [
            'id' => $this->id,
            'user_one_items' => FurnitureResource::collection($userOneItems),
            'user_two_items' => FurnitureResource::collection($userTwoItems),
            'traded_at' => \Carbon\Carbon::createFromTimestamp($this->timestamp)->toDateTimeString(),
        ];
    }
}
