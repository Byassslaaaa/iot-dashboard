<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LidEvent extends Model
{
    protected $fillable = [
        'trash_bin_id',
        'event_type',
        'duration_seconds',
    ];

    public function trashBin(): BelongsTo
    {
        return $this->belongsTo(TrashBin::class);
    }
}
