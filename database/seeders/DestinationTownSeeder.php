<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DestinationTownSeeder extends Seeder
{
    /**
     * Seed the application's destination towns.
     */
    public function run(): void
    {
        $now = now();

        $towns = [
            'ကွန်ဟိန်း',
            'ကာလိ',
            'ကျိုင်းတုံ',
            'ကျေးသီး',
            'ခိုလန်',
            'ဆင်မောင်း',
            'ဆီဆိုင်',
            'ဆိုက်ခေါင်',
            'တန့်ယန်း',
            'တုံလော',
            'တောင်ကြီး',
            'တာကော်',
            'တာချီလိတ်',
            'နမ့်ခမ်း',
            'နမ့်စန်',
            'နားကောင်းမှူး',
            'ပင်လုံ',
            'ပြင်ဦးလွင်',
            'ပင်းတယ',
            'မူဆယ်',
            'မန္တလေး',
            'မယ်ယန်း',
            'မိုင်းခတ်',
            'မိုင်းကိုင်',
            'မိုင်းဖြတ်',
            'မိုင်းပျဉ်း',
            'မိုင်းပန်',
            'မိုင်းရယ်',
            'မိုင်းရှူး',
            'မိုင်းယန်း',
            'မိုးနဲ',
            'လဲချား',
            'လွိုင်လင်',
            'လင်းခေး',
            'လားရှိုး',
            'ရန်ကုန်',
            'ရပ်စောက်',
            'ရွှေညောင်',
            'သိန်းနီ',
            'သီပေါ',
            'အင်တော',
            'အေးသာယာ',
            'ကျောက်ဂူ',
        ];

        foreach ($towns as $index => $townName) {
            DB::table('towns')->updateOrInsert(
                [
                    'town_name' => $townName,
                    'type' => 'destination',
                ],
                [
                    'city_code' => null,
                    'sort_order' => $index + 1,
                    'updated_at' => $now,
                    'created_at' => $now,
                ]
            );
        }
    }
}
