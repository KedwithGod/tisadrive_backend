<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\CancellationReason;

class CancellationReasonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $cancelation_reason = [
       [ 'user_type' => 'user',
         'transport_type' => 'taxi',
        'payment_type' => 'free',
        'arrival_status' => 'before',
        'reason' => 'Driver taking too long',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'taxi',
        'payment_type' => 'free',
        'arrival_status' => 'after',
        'reason' => 'Driver Under Influence',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'taxi',
        'payment_type' => 'compensate',
        'arrival_status' => 'after',
        'reason' => 'Driver Took too much time',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'taxi',
        'payment_type' => 'compensate',
        'arrival_status' => 'before',
        'reason' => 'Do not want to say',
        'active' => 1,
        'company_key' => null,],

        [ 'user_type' => 'driver',
         'transport_type' => 'taxi',
        'payment_type' => 'free',
        'arrival_status' => 'before',
        'reason' => 'Cancel for User',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'taxi',
        'payment_type' => 'free',
        'arrival_status' => 'after',
        'reason' => 'User Not Responsive',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'taxi',
        'payment_type' => 'compensate',
        'arrival_status' => 'before',
        'reason' => 'Do not want to say',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'taxi',
        'payment_type' => 'compensate',
        'arrival_status' => 'after',
        'reason' => 'User Under Influence',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'delivery',
        'payment_type' => 'free',
        'arrival_status' => 'before',
        'reason' => 'Driver taking too long to arrive',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'delivery',
        'payment_type' => 'free',
        'arrival_status' => 'after',
        'reason' => 'Driver Under Influence',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'delivery',
        'payment_type' => 'compensate',
        'arrival_status' => 'after',
        'reason' => 'Driver Took too much time',
        'active' => 1,
        'company_key' => null,],
       [ 'user_type' => 'user',
         'transport_type' => 'delivery',
        'payment_type' => 'compensate',
        'arrival_status' => 'before',
        'reason' => 'Do not want to say',
        'active' => 1,
        'company_key' => null,],
        ['user_type' => 'driver',
         'transport_type' => 'delivery',
        'payment_type' => 'free',
        'arrival_status' => 'before',
        'reason' => 'Cancel for User',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'delivery',
        'payment_type' => 'free',
        'arrival_status' => 'after',
        'reason' => 'User Not Responsive',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'delivery',
        'payment_type' => 'compensate',
        'arrival_status' => 'before',
        'reason' => 'Do not want to say',
        'active' => 1,
        'company_key' => null,],
        [ 'user_type' => 'driver',
         'transport_type' => 'delivery',
        'payment_type' => 'compensate',
        'arrival_status' => 'after',
        'reason' => 'User Took too much time',
        'active' => 1,
        'company_key' => null,],
    ];

    public function run()
    {
      $created_params = $this->cancelation_reason;
     
      $value = CancellationReason::first();

        foreach ($created_params as $reason) 
        {
          $cancellation = CancellationReason::firstOrCreate($reason);
          $cancellation->cancellationReasonTranslationWords()->delete();
          $translationData = ['name' => $cancellation->reason, 'locale' => 'en', 'cancellation_reason_id' => $cancellation->id];
          $translations_data['en'] = new \stdClass();
          $translations_data['en']->locale = 'en';
          $translations_data['en']->name = $cancellation->reason;
          $cancellation->cancellationReasonTranslationWords()->insert($translationData);
          $cancellation->translation_dataset = json_encode($translations_data);
          $cancellation->save();
        }
    }
}
