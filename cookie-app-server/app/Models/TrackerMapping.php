<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerMapping extends Model
{
    protected $fillable = [
        'tracker_id','origin','site_user_id',
        'first_ip','last_ip','first_user_agent','last_user_agent',
        'first_seen','last_seen','visit_count'
    ];

    protected $casts = [
        'first_seen' => 'datetime',
        'last_seen'  => 'datetime',
    ];

    public function events()
    {
        return $this->hasMany(TrackerEvent::class);
    }
}
