<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CltLayer extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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
        'thickness'   => 'decimal:4',
        'width'       => 'decimal:4',
        'angle'       => 'decimal:4',
    ];

    /**
     * A CLT Layer belongs to a CLT Layup.
     */
    public function layup(): BelongsTo
    {
        return $this->belongsTo(CltLayup::class, 'layup_id');
    }
}
