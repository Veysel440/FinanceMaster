<?php

namespace App\Http\Resources\Budget;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'category'   => $this->category ? $this->category->name : null,
            'category_id'=> $this->category_id,
            'amount'     => $this->amount,
            'month'      => $this->month->format('Y-m'),
            'status'     => $this->status ?? null,
        ];
    }
}
