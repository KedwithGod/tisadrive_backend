<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\ComplaintTitle;

class ComplaintTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    protected $complaint_title = [
    ['user_type' => 'User',
    'title' => 'Driver Rash Driving',
    'complaint_type' => 'request_help',
    'active' => 1,
    ],
    ['user_type' => 'User',
    'title' => 'Vehicle Not Clean',
    'complaint_type' => 'general',
    'active' => 1,
    ],
    ['user_type' => 'User',
    'title' => 'Vehicle Uncomfortable',
    'complaint_type' => 'general',
    'active' => 1,
    ],
    ['user_type' => 'User',
    'title' => 'Driver Under Influence',
    'complaint_type' => 'request_help',
    'active' => 1,
    ],
    ['user_type' => 'Driver',
    'title' => 'User Not Reachable',
    'complaint_type' => 'general',
    'active' => 1,
    ],
    ['user_type' => 'Driver',
    'title' => 'User Cancelled',
    'complaint_type' => 'general',
    'active' => 1,
    ],
    ['user_type' => 'Driver',
    'title' => 'User Double Booked',
    'complaint_type' => 'request_help',
    'active' => 1,
    ],
    ['user_type' => 'Driver',
    'title' => 'User Under Influence',
    'complaint_type' => 'request_help',
    'active' => 1,
    ]
    ];


    public function run()
    {

      $value = ComplaintTitle::first();
      if(!$value) {

        $created_params = $this->complaint_title;


        foreach ($created_params as $title) 
        {
         $complaintTitle = ComplaintTitle::firstOrCreate($title);
         $complaintTitle->complaintTitleTranslationWords()->delete();
         $translationData = ['name' => $complaintTitle->title, 'locale' => 'en', 'complaint_title_id' => $complaintTitle->id];
         $translations_data['en'] = new \stdClass();
         $translations_data['en']->locale = 'en';
         $translations_data['en']->name = $complaintTitle->title;
         $complaintTitle->complaintTitleTranslationWords()->insert($translationData);
         $complaintTitle->translation_dataset = json_encode($translations_data);
         $complaintTitle->save();
 
        }
      }
  }
}
