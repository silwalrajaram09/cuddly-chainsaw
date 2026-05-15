<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommitteeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id,
            'children' => CommitteeResource::collection($this->whenLoaded('children')),
            'pivot' => $this->whenPivotLoaded('user_committee', function () {
                return [
                    'order_no' => $this->pivot->order_no,
                ];
            }),
        ];
    }
}
