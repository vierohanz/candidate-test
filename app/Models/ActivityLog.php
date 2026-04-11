<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // activity_logs only has created_at, not updated_at
    public $timestamps = false;

    protected $fillable = [
        'action',
        'entity_type',
        'entity_id',
        'before',
        'after',
        'ip_address',
        'description',
    ];

    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'created_at' => 'datetime',
    ];
}
