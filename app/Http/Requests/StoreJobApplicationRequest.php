<?php

namespace App\Http\Requests;

use App\Models\Vacancy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobApplicationRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'vacancy_id' => ['nullable', 'integer', Rule::in(Vacancy::query()->open()->pluck('id')->all())],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40'],
            'email' => ['nullable', 'email', 'max:190'],
            'message' => ['nullable', 'string', 'max:2000'],
            'cv' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'website' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'vacancy_id.in' => 'That vacancy has closed. Choose another, or send a general application.',
            'name.required' => 'Please tell us your name.',
            'phone.required' => 'Please give us a number to reach you on.',
            'cv.required' => 'Please attach your CV.',
            'cv.mimes' => 'Your CV must be a PDF or Word document.',
            'cv.max' => 'Your CV must be 5 MB or smaller.',
            'cv.uploaded' => 'Your CV could not be uploaded. It must be 5 MB or smaller.',
        ];
    }
}
