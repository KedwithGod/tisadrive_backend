<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Master\MailTemplate;

class MailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
 protected $mailTemplate = [

    /*Welcome Mail user*/
        [  'mail_type' => 'welcome_mail',
            'active' => 1,
            'description' => '----',

           'translation_dataset' => 

                                    '[{"subject":"welcome_mail","lang_code":"en","code":"en","description":"<p>Hello $user_name<\/p>\n\n            <p>Thank you for joining MI Softwares! We are thrilled to have you as a part of our community.<\/p>\n\n            <p>Our mission is to mobility. We hope that you will find our products\/services to be useful and enjoyable.<\/p>\n\n            <p>To get started, please take a few moments to explore our website and familiarize yourself with our offerings. If you have any questions or concerns, our customer support team is always here to help.<\/p>\n\n            <p>We look forward to working with you and providing you with a top-notch experience.<\/p>\n\n            <p>Best regards,<\/p>\n\n            <p>MI Softwares<\/p>","facebook":1,"twitter":1,"linkedin":1,"googleplus":1,"button_enable":0,"banner_enable":0,"footer_content":"Please contact us for any queries; we\u2019re always happy to help.","footer_copyrights":"\u00a9 2023 6amMart. All rights reserved."}]'

        ],
    /*Welcome Mail Driver*/
        [  'mail_type' => 'welcome_mail_driver',
            'active' => 1,
            'description' => '---',
            'translation_dataset' => 
                                    '[{"subject":"welcome_mail_driver","lang_code":"en","code":"en","description":"<p>Dear $user_name,<\/p>\n\n        <p>Congratulations on becoming a newly registered driver! We are excited to welcome you to the world of driving and wanted to take a moment to extend our warmest greetings.<\/p>\n\n        <p>As a registered driver, you now have the opportunity to explore new destinations, embrace independence, and experience the joys of the open road. We hope this new chapter brings you many memorable adventures and experiences.<\/p>\n\n        <p>Please remember to prioritize safety as you embark on your driving journey. Observe traffic laws, wear your seatbelt, and remain attentive at all times. Safe driving not only protects you but also ensures the well-being of others around you.<\/p>\n\n        <p>If you ever have any questions or need assistance along the way, our team is here to support you. Don&#39;t hesitate to reach out to us; we&#39;re more than happy to help.<\/p>\n\n        <p>Once again, congratulations on your registration! Enjoy the freedom and excitement that driving offers. We wish you safe travels and an incredible journey ahead.<\/p>\n\n        <p>Best regards,<\/p>\n\n        <p>Tagxi<\/p>","facebook":1,"twitter":1,"linkedin":1,"googleplus":1,"button_enable":0,"banner_enable":0,"footer_content":"Please contact us for any queries; we\u2019re always happy to help.","footer_copyrights":"\u00a9 2023 6amMart. All rights reserved."}]'

        ],
 
    ];




    public function run()
    {
       $created_params = $this->mailTemplate;

            $value = MailTemplate::first();

            if(!$value)
            {
                foreach ($created_params as $mailTemplate)
                {
                    MailTemplate::firstorcreate($mailTemplate);
                }
            }

        }    
    }
