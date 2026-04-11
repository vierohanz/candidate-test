<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CltLayer extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    protected $table = 'clt_layers';

    protected $fillable = [
        'layup_id',
        'layer_order',
        'thickness',
        'width',
        'angle',
    ];

    protected $casts = [
        'layer_order' => 'integer',
        'thickness' => 'decimal:2',
        'width' => 'decimal:2',
        'angle' => 'decimal:2',
    ];

    /**
     * A CLT Layer belongs to a CLT Layup.
     */
    public function layup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class, 'layup_id');
    }
}
