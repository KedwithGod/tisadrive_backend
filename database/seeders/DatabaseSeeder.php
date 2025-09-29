<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\LandingSiteSeeder;
use DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesAndPermissionsSeeder::class);
        $this->call(CountriesTableSeeder::class);
        $this->call(TimeZoneSeeder::class);        
        $this->call(DefaultLanguageSeeder::class);        
        $this->call(AdminSeeder::class);        
        $this->call(SettingsSeeder::class);
        $this->call(CancellationReasonSeeder::class);
        $this->call(ComplaintTitleSeeder::class);
        $this->call(GoodsTypeSeeder::class);
        // $this->call(LanguageTableSeeder::class);
        $this->call(MailTemplateSeeder::class);
        $this->call(ThirdPartySettingSeeder::class);
        // $this->call(RatingSeeder::class);
        // $this->call(LandingSiteSeeder::class); 
        $this->call(OnboardingSeeder::class);
        $this->call(NotificationChannelSeeder::class);

        if (env('APP_FOR') == 'demo') {
            $this->call(LandingHomeSeeder::class);
        } else {
            $this->call(LandingSiteSeeder::class);
        }

        

    }
}
