<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollaborationReminder extends Model
{
    protected $fillable = [
  'dedupe_key',
  'user_id',
  'collaboration_id',
  'send_at',
  'channel',
  'status',
  'sent_at',
  'last_error',
];


    protected $casts = [
  'send_at' => 'datetime',
  'sent_at' => 'datetime',
];


    public function collaboration()
    {
        return $this->belongsTo(Collaboration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
