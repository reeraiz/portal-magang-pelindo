<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'intern_id', 'division', 'avatar', 'internship_start_date', 'internship_end_date', 'mentor_id', 'internship_type_id', 'education_level_id', 'university_id', 'gender_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'internship_start_date' => 'date',
            'internship_end_date' => 'date',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function logbooks()
    {
        return $this->hasMany(Logbook::class);
    }

    public function mentor()
    {
        return $this->belongsTo(User::class, 'mentor_id');
    }

    public function mentees()
    {
        return $this->hasMany(User::class, 'mentor_id');
    }

    public function internshipType()
    {
        return $this->belongsTo(InternshipType::class);
    }

    public function educationLevel()
    {
        return $this->belongsTo(EducationLevel::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class);
    }
}
