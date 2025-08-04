<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'category_id', 'amount', 'month',
    ];

    protected $casts = [
        'month' => 'date:Y-m-01',
    ];

    public function user()      { return $this->belongsTo(User::class); }
    public function category()  { return $this->belongsTo(Category::class); }
}
