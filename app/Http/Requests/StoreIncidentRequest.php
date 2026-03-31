<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreIncidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check() && in_array(
            Auth::user()->role?->name,
            ['discipline_coordinator', 'principal', 'assistant_principal', 'adviser']
        );
    }

    public function rules(): array
    {
        return [
            'incident_date' => 'required|date|before_or_equal:today',
            'incident_time' => 'nullable|date_format:H:i',
            'location' => 'required|string|max:500|regex:/^[a-zA-Z0-9\s\-,\.]+$/',
            'description' => 'required|string|min:10|max:5000',
            'students' => 'required|array|min:1|max:50',
            'students.*' => 'required|integer|exists:students,id',
            'violation_category_id' => 'nullable|integer|exists:violation_categories,id',
            'is_custom_violation' => 'required|boolean',
            'violation_clause_id' => 'nullable|required_unless:is_custom_violation,1|integer|exists:violation_clauses,id',
            'custom_violation_description' => 'nullable|required_if:is_custom_violation,1|string|max:1000',
            'custom_violation_category_id' => 'nullable|required_if:is_custom_violation,1|integer|exists:violation_categories,id',
            'narrative_reports' => 'nullable|array|max:50',
            'narrative_reports.*' => 'nullable|string|max:10000',
            'narrative_files' => 'nullable|array|max:10',
            'narrative_files.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120|mimetypes:application/pdf,image/jpeg,image/png',
            'witnesses' => 'nullable|array|max:20',
            'witnesses.*' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'incident_date.before_or_equal' => 'Incident date cannot be in the future.',
            'location.regex' => 'Location contains invalid characters.',
            'description.min' => 'Description must be at least 10 characters.',
            'students.required' => 'At least one student must be selected.',
            'students.max' => 'Maximum 50 students can be involved in one incident.',
            'narrative_files.*.mimes' => 'Only PDF, JPG, JPEG, and PNG files are allowed.',
            'narrative_files.*.max' => 'Each file must not exceed 5MB.',
        ];
    }

    protected function prepareForValidation()
    {
        // Sanitize inputs to prevent XSS
        if ($this->has('description')) {
            $this->merge([
                'description' => strip_tags($this->description),
            ]);
        }

        if ($this->has('location')) {
            $this->merge([
                'location' => strip_tags($this->location),
            ]);
        }

        if ($this->has('narrative_reports')) {
            $this->merge([
                'narrative_reports' => array_map('strip_tags', $this->narrative_reports ?? []),
            ]);
        }
    }
}
