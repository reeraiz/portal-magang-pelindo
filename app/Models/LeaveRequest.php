<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'start_date',
        'end_date',
        'notes',
        'attachment',
        'status',
        'admin_note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relasi ke model User (intern yang mengajukan).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk menghitung durasi hari izin (termasuk hari pertama & terakhir).
     */
    public function getDurationDaysAttribute(): int
    {
        if (! $this->start_date || ! $this->end_date) {
            return 0;
        }

        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }
}
