<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'division' => ['nullable', 'string', 'max:255'],
            'internship_start_date' => ['nullable', 'date'],
            'internship_end_date' => ['nullable', 'date', 'after_or_equal:internship_start_date'],
            'internship_type_id' => ['nullable', 'exists:internship_types,id'],
            'education_level_id' => ['nullable', 'exists:education_levels,id'],
            'university_id' => ['nullable', 'exists:universities,id'],
            'gender_id' => ['nullable', 'exists:genders,id'],
        ];
    }
}
