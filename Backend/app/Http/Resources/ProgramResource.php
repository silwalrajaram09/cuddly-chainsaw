<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'no_of_days' => $this->no_of_days,
            'location' => $this->location,
            'country_id' => $this->country_id,
            // 'country' => new CountryResource($this->whenLoaded('country')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            // 'members_count' => $this->whenLoaded('members', function () {
            //     return $this->members->count();
            // }),
            'members_count' => $this->members_count ?? 0,
            'status' => $this->status,
            'duration' => $this->duration,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}