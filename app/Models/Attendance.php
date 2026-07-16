<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'date', 'check_in', 'check_out', 'status', 'location', 'notes', 'attachment'])]
class Attendance extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNotesAttribute($value)
    {
        if ($value && preg_match('/Terlambat\s+([\d\.]+)\s+menit/i', $value, $matches)) {
            $minutes = (int) round((float) $matches[1]);

            return 'Terlambat '.$minutes.' menit';
        }

        return $value;
    }
}
