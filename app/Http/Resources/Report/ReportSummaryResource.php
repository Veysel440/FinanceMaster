<?php

namespace App\Http\Resources\Report;


use Illuminate\Http\Resources\Json\JsonResource;

class ReportSummaryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'income'  => $this['income'],
            'expense' => $this['expense'],
            'balance' => $this['balance'],
        ];
    }
}
