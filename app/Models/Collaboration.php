<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Collaboration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'title',
        'status',
        'price',
        'start_date',
        'end_date',
        'follow_up_date',
        'notes',
    ];

    public function sponsor()
    {
        return $this->belongsTo(Sponsor::class);
    }

    public function user()
    {
         return $this->belongsTo(User::class);
    }
}
