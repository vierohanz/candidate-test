<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CltLayup extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($layup) {
            $layup->layers()->delete();
        });
    }

    protected $table = 'clt_layups';

    protected $fillable = [
        'supplier_id',
        'name',
    ];

    /**
     * A CLT Layup belongs to a Supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    /**
     * A CLT Layup has many CLT Layers, ordered by layer_order ascending.
     */
    public function layers(): HasMany
    {
        return $this->hasMany(CltLayer::class, 'layup_id')->orderBy('layer_order');
    }
}
