<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'date', 'time', 'category', 'title', 'description', 'status', 'reject_reason', 'attachment', 'attachments', 'feedback', 'grade'])]
class Logbook extends Model
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

    public function getAttachmentsListAttribute(): array
    {
        if (! empty($this->attachments)) {
            $decoded = json_decode($this->attachments, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }
        if (! empty($this->attachment)) {
            $decoded = json_decode($this->attachment, true);
            if (is_array($decoded)) {
                return $decoded;
            }

            return [$this->attachment];
        }

        return [];
    }
}
