<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyStatistic extends Model
{
    protected $fillable = [
        'trash_bin_id',
        'date',
        'lid_open_count',
        'object_detect_count',
        'full_alerts_count',
        'earnings',
        'costs',
    ];

    protected $casts = [
        'date' => 'date',
        'earnings' => 'decimal:2',
        'costs' => 'decimal:2',
    ];

    public function trashBin(): BelongsTo
    {
        return $this->belongsTo(TrashBin::class);
    }
}
