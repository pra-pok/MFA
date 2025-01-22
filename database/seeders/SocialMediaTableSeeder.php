<?php

namespace Database\Seeders;

use App\Models\SocialMedia;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SocialMediaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('social_medias')->insert([
            'name' => 'Facebook',
            'icon' => "bx bxl-facebook",
        ]);
        DB::table('social_medias')->insert([
            'name' => 'Instagram',
            'icon' => "bx bxl-instagram",
        ]);
        DB::table('social_medias')->insert([
            'name' => 'Twitter',
            'icon' => "bx bxl-twitter",
        ]);
        DB::table('social_medias')->insert([
        'name' => 'Youtube',
        'icon' => "bx bxl-youtube",
        ]);
        DB::table('social_medias')->insert([
            'name' => 'Linkedin',
            'icon' => "bx bxl-linkedin",
        ]);
        DB::table('social_medias')->insert([
            'name' => 'Tiktok',
            'icon' => "bx bxl-tiktok",
        ]);

    }
}
