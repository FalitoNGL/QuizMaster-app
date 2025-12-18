<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'time_limit' => 'required|integer|min:1|max:180',
            'is_active' => 'boolean',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kuis wajib diisi.',
            'time_limit.required' => 'Batas waktu wajib diisi.',
            'ends_at.after' => 'Waktu berakhir harus setelah waktu mulai.',
        ];
    }
}
