<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuizRequest extends FormRequest
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
            'randomize_questions' => 'boolean',
            'question_limit' => 'nullable|integer|min:1',
            'starts_at' => 'nullable|date',
            'ends_at' => 'nullable|date|after:starts_at',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul kuis wajib diisi.',
            'title.max' => 'Judul kuis maksimal 255 karakter.',
            'time_limit.required' => 'Batas waktu wajib diisi.',
            'time_limit.min' => 'Batas waktu minimal 1 menit.',
            'time_limit.max' => 'Batas waktu maksimal 180 menit.',
            'thumbnail.image' => 'File harus berupa gambar.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
            'ends_at.after' => 'Waktu berakhir harus setelah waktu mulai.',
        ];
    }
}
