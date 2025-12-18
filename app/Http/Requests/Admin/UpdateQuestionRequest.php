<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string',
            'type' => 'required|in:single_choice,multiple_choice,ordering,matching',
            'points' => 'required|integer|min:1|max:100',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp3,wav|max:5120',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string',
            'options.*.is_correct' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Isi soal wajib diisi.',
            'type.required' => 'Tipe soal wajib dipilih.',
            'points.required' => 'Bobot nilai wajib diisi.',
            'options.required' => 'Opsi jawaban wajib diisi.',
            'options.min' => 'Minimal 2 opsi jawaban.',
            'media.mimes' => 'File harus berupa gambar (jpg, png, gif) atau audio (mp3, wav).',
            'media.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
