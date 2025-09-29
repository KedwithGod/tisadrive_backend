<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;
use App\Models\Languages;

class DefaultLanguageSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $language = Languages::first();

        $languagesToSeed = [
            [
                'code'=>'en',
                'name'=>'English',
                'direction'=>'ltr',
                'default_status'=>true
            ],
            [
                'code'=>'ar',
                'name'=>'Arabic',
                'direction'=>'rtl',
                'default_status'=>false
            ],
            [
                'code'=>'fr',
                'name'=>'French',
                'direction'=>'ltr',
                'default_status'=>false
            ],
            [
                'code'=>'es',
                'name'=>'Spanish',
                'direction'=>'ltr',
                'default_status'=>false
            ],
        ];
        if(!$language){

            foreach ($languagesToSeed as $lang) {
                Languages::create($lang);
            }
        }


    }

}
