<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackerEvent extends Model
{
    protected $fillable = ['tracker_mapping_id','origin','path','ip','user_agent','meta'];

    protected $casts = [
        'meta' => 'array'
    ];

    public function mapping()
    {
        return $this->belongsTo(TrackerMapping::class, 'tracker_mapping_id');
    }
}
