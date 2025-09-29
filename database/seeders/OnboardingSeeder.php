<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Onboarding;
use Illuminate\Support\Str;

class OnboardingSeeder extends Seeder
{
    public function run()
    { 
        $onboarding = Onboarding::first();
        // $userName = User::latest()->value('name') ?? 'User'; 

        if (!$onboarding) {
            // Seed the notification channels
            \DB::table('onboarding_screen')->insert([
                [
                    'id' => Str::uuid(),
                    'sn_o' => 1,
                    'screen' => 'user',
                    'order' => 1,
                    'title' => 'Assurance',
                    'onboarding_image' => 'onboard1.jpg',
                    'description' => 'Customer safety first, Always and forever our pledge, Your well-being, our priority, With you every step, edge to edge.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 2,
                    'screen' => 'user',
                    'order' => 2,
                    'title' => 'Clarity',
                    'onboarding_image' => 'onboard2.jpg',
                    'description' => 'Fair pricing, crystal clear, Your trust, our promise sincere. With us, youll find no hidden fee, Transparency is our guarantee.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 3,
                    'screen' => 'user',
                    'order' => 3,
                    'title' => 'Intuitive',
                    'onboarding_image' => 'onboard3.jpg',
                    'description' => 'Seamless journeys, Just a tap away, Explore hassle-free, Every step of the way.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 4,
                    'screen' => 'user',
                    'order' => 4,
                    'title' => 'Support',
                    'onboarding_image' => 'onboard4.jpg',
                    'description' => 'Embark on your journey with confidence, knowing that our commitment to your satisfaction is unwavering',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 5,
                    'screen' => 'driver',
                    'order' => 1,
                    'title' => 'Assurance',
                    'onboarding_image' => 'driverOnboard1.png',
                    'description' => 'Customer safety first, Always and forever our pledge, Your well-being, our priority, With you every step, edge to edge.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 6,
                    'screen' => 'driver',
                    'order' => 2,
                    'title' => 'Clarity',
                    'onboarding_image' => 'driverOnboard2.png',
                    'description' => 'Fair pricing, crystal clear, Your trust, our promise sincere. With us, youll find no hidden fee, Transparency is our guarantee.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 7,
                    'screen' => 'driver',
                    'order' => 3,
                    'title' => 'Intuitive',
                    'onboarding_image' => 'driverOnboard3.png',
                    'description' => 'Seamless journeys, Just a tap away, Explore hassle-free, Every step of the way.',
                    'active' => 1
                ],
                //owner
                [
                    'id' => Str::uuid(),
                    'sn_o' => 8,
                    'screen' => 'owner',
                    'order' => 1,
                    'title' => 'Intuitive',
                    'onboarding_image' => 'ownerOnboard1.png',
                    'description' => 'Seamless journeys, Just a tap away, Explore hassle-free, Every step of the way.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 9,
                    'screen' => 'owner',
                    'order' => 2,
                    'title' => 'Assurance',
                    'onboarding_image' => 'ownerOnboard2.png',
                    'description' => 'Customer safety first, Always and forever our pledge, Your well-being, our priority, With you every step, edge to edge.',
                    'active' => 1
                ],
                [
                    'id' => Str::uuid(),
                    'sn_o' => 10,
                    'screen' => 'owner',
                    'order' => 3,
                    'title' => 'Clarity',
                    'onboarding_image' => 'ownerOnboard3.png',
                    'description' => 'Fair pricing, crystal clear, Your trust, our promise sincere. With us, youll find no hidden fee, Transparency is our guarantee.',
                    'active' => 1
                ],
        

            ]);
        }

    //     // Insert translations
        $onboarding = \DB::table('onboarding_screen')->get();

                    foreach ($onboarding as $onboardData) 
            {
                // Check if the notification channel already exists based on a unique attribute (like 'email_subject')
                $onboarding = Onboarding::where('sn_o', $onboardData->sn_o)->first();

                if ($onboarding) {
                    // Update existing notification channel
                    // $onboarding->update($onboardData);
                    $onboarding->update((array) $onboardData);

                    // Delete old translations for the notification channel
                    $onboarding->onboardingTranslationWords()->delete();
                } else {
                    // Create new notification channel if it doesn't exist
                    // $onboarding = onboarding::create($onboardData);
                    $onboarding = Onboarding::create((array) $onboardData);
                }

                // Prepare translation data for the 'en' locale
                $translationData = [
                    'title' => $onboardData->title,
                    'description' => $onboardData->description,
                    'locale' => 'en',
                    'onboarding_screen_id' => $onboarding->id,
                ];

                // Store the translations as objects for 'en' locale
                $translations_data['en'] = new \stdClass();
                $translations_data['en']->locale = 'en';
                $translations_data['en']->title = $onboardData->title;
                $translations_data['en']->description = $onboardData->description;

                // Insert the translation data into the related translation table
                $onboarding->onboardingTranslationWords()->create($translationData);

                // Store the translation dataset in JSON format for the notification channel
                $onboarding->translation_dataset = json_encode($translations_data);
                
                // Save the updated notification channel with its translations
                $onboarding->save();
            }

    }
   
}
