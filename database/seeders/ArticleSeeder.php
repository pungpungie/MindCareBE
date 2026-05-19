<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;

class ArticleSeeder extends Seeder
{
    public function run(): void
    {
        Article::truncate();

        Article::insert([
            [
                'title' => '5 Cara Mengurangi Stres di Tempat Kerja',
                'content' => 'Stres di tempat kerja adalah hal yang umum. Berikut 5 cara menguranginya: 1. Atur napas... 2. Istirahat sejenak... 3. Olahraga ringan... 4. Bicara dengan rekan... 5. Buat prioritas tugas...',
                'category' => 'Self Care',
                'mood_tag' => 'stres_ringan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Meditasi untuk Pemula: Panduan 10 Menit',
                'content' => 'Meditasi tidak harus sulit. Ikuti panduan ini: 1. Duduk nyaman... 2. Tutup mata... 3. Fokus pada napas... 4. Biarkan pikiran berlalu... 5. Kembali ke napas...',
                'category' => 'Meditasi',
                'mood_tag' => 'stres_sedang',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Olahraga Ringan untuk Kesehatan Mental',
                'content' => 'Olahraga terbukti meningkatkan mood. Coba olahraga ringan ini: Jalan kaki 30 menit, stretching, yoga, atau bersepeda santai...',
                'category' => 'Olahraga',
                'mood_tag' => 'stres_ringan',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Mengenali Tanda-tanda Depresi',
                'content' => 'Depresi bisa dikenali dari beberapa tanda: Kehilangan minat, perubahan pola tidur, merasa tidak berharga, sulit konsentrasi... Jika mengalaminya, segera konsultasi ke profesional.',
                'category' => 'Edukasi',
                'mood_tag' => 'stres_berat',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'title' => 'Journaling: Terapi Menulis untuk Kesehatan Mental',
                'content' => 'Menulis jurnal adalah cara efektif untuk mengelola emosi. Tips: Tulis apa adanya, jangan sensor diri sendiri, tulis setiap hari meski hanya 5 menit...',
                'category' => 'Self Care',
                'mood_tag' => 'stres_sedang',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);

        echo "Seeder selesai! " . Article::count() . " artikel dibuat.\n";
    }
}