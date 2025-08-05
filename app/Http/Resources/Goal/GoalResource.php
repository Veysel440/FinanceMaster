<?php

namespace App\Http\Resources\Goal;

use Illuminate\Http\Resources\Json\JsonResource;

class GoalResource extends JsonResource
{
    public function toArray($request)
    {
        $progress = $this->target_amount > 0
            ? round(($this->current_amount / $this->target_amount) * 100, 2)
            : 0;

        return [
            'id'             => $this->id,
            'title'          => $this->title,
            'target_amount'  => $this->target_amount,
            'current_amount' => $this->current_amount,
            'end_date'       => $this->end_date?->format('Y-m-d'),
            'progress'       => $progress,
            'status'         => $progress >= 100 ? 'completed' : 'ongoing',
        ];
    }
}
