<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
    ];

    /**
     * A Supplier has many CLT Layups.
     */
    public function layups(): HasMany
    {
        return $this->hasMany(CltLayup::class, 'supplier_id');
    }

    /**
     * Convenience: all Layers belonging to this Supplier (through Layups).
     */
    public function layers(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(CltLayer::class, CltLayup::class, 'supplier_id', 'layup_id');
    }
}
