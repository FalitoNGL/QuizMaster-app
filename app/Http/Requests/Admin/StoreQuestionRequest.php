<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'type' => 'required|in:multiple_choice,true_false,essay,matching,ordering',
            'points' => 'required|integer|min:1|max:100',
            'options' => 'required_if:type,multiple_choice,true_false,matching,ordering|array',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'boolean',
            'correct_answer' => 'required_if:type,essay|nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Isi soal wajib diisi.',
            'type.required' => 'Tipe soal wajib dipilih.',
            'type.in' => 'Tipe soal tidak valid.',
            'points.required' => 'Bobot nilai wajib diisi.',
            'points.min' => 'Bobot nilai minimal 1.',
            'points.max' => 'Bobot nilai maksimal 100.',
            'options.required_if' => 'Opsi jawaban wajib diisi untuk tipe soal ini.',
        ];
    }
}
