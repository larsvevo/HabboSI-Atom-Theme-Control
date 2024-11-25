<?php

namespace Atom\Theme\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class WebsiteArticleResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'short_story' => $this->short_story,
            'full_story' => $this->full_story,
            'image' => Storage::url($this->image),
            'user' => UserResource::make($this->whenLoaded('user')),
            'redirect_url' => route('community.articles.show', $this),
            'resource_url' => route('api.public.website-articles.show', $this),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
