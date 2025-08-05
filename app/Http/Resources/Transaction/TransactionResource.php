<?php

namespace App\Http\Resources\Transaction;

use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'category'    => $this->category ? $this->category->name : null,
            'category_id' => $this->category_id,
            'type'        => $this->type,
            'amount'      => $this->amount,
            'date'        => $this->date?->format('Y-m-d'),
            'description' => $this->description,
        ];
    }
}
