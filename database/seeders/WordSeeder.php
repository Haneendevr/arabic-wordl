<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Word;

class WordSeeder extends Seeder
{
    public function run(): void
    {
        // قائمة كلمات عربية خماسية الحروف بدون تشكيل
        $words = [
            'طاولة', 'مكتبة', 'مدرسة', 'سيارة', 'حاسوب', 
            'تفاحة', 'حديقة', 'طائرة', 'جامعة', 'نافذة',
            'زهرة', 'كتابة', 'قراءة', 'شجرة', 'مدينة',
            'سحابة', 'منزل', 'طبيب', 'صديق', 'مستند',
            'حقيقة', 'عظيمة', 'جميلة', 'عالمي', 'معرفة'
        ];

        foreach ($words as $word) {
            Word::firstOrCreate(['word' => $word]);
        }
    }
}
