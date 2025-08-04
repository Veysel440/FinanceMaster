<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,
            'profile_photo_url' => $this->profile_photo_url,
            'currency'   => $this->currency,
            'locale'     => $this->locale,
            'created_at' => $this->created_at,
        ];
    }
}
