<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->user_id,
            'full_name' => $this->full_name,
            'designation' => $this->designation,
            'email' => $this->email,
            'type' => $this->type,
            'member_type' => $this->member_type,
            'started_date' => $this->started_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'address' => $this->address,
            'fb_link' => $this->fb_link,
            'youtube' => $this->youtube,
            'tiktok' => $this->tiktok,
            'profile_description' => $this->profile_description,
            'photo_url' => $this->photo_path ? asset('storage/' . $this->photo_path) : null,
            'committees' => CommitteeResource::collection($this->whenLoaded('committees')),
            'programs' => ProgramResource::collection($this->whenLoaded('programs')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
