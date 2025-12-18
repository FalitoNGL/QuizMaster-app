<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus semua kategori lama
        Category::truncate();

        $categories = [
            [
                'name' => 'Etos Sandi III',
                'slug' => 'etos-sandi-iii',
                'icon' => '🔒',
                'color' => '#ef4444', // red
                'description' => 'Quiz tentang etika dan standar keamanan sandi.',
            ],
            [
                'name' => 'Pemrograman Jaringan',
                'slug' => 'pemrograman-jaringan',
                'icon' => '🌐',
                'color' => '#3b82f6', // blue
                'description' => 'Quiz tentang pemrograman aplikasi berbasis jaringan.',
            ],
            [
                'name' => 'Kriptografi Terapan',
                'slug' => 'kriptografi-terapan',
                'icon' => '🔐',
                'color' => '#8b5cf6', // purple
                'description' => 'Quiz tentang implementasi algoritma kriptografi.',
            ],
            [
                'name' => 'Pemrograman Lanjutan',
                'slug' => 'pemrograman-lanjutan',
                'icon' => '💻',
                'color' => '#22c55e', // green
                'description' => 'Quiz tentang konsep pemrograman tingkat lanjut.',
            ],
            [
                'name' => 'Sistem Operasi & Virtualisasi',
                'slug' => 'sistem-operasi-virtualisasi',
                'icon' => '🖥️',
                'color' => '#f97316', // orange
                'description' => 'Quiz tentang sistem operasi dan teknologi virtualisasi.',
            ],
            [
                'name' => 'Sistem Telekomunikasi',
                'slug' => 'sistem-telekomunikasi',
                'icon' => '📡',
                'color' => '#06b6d4', // cyan
                'description' => 'Quiz tentang sistem dan jaringan telekomunikasi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        $this->command->info('Categories berhasil diperbarui!');
    }
}
