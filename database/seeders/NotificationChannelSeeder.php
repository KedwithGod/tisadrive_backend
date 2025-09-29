<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\NotificationChannel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationChannelSeeder extends Seeder
{
    
    public function run()
    { 

        $notification =[
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'New Customer Registration',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on customer registration',
                    'push_notification' => 0,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Registration Mail',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '<p>Hello {name}</p>
                                    <p>Thank you for signing up with us, your trusted taxi app. Your registration was successful, and we are excited to have you on board.</p>
                                    <p>Your Account Details</p>
                                    <p>Email: {email}</p>
                                    <p>Mobile Number: {mobile}</p>
                                    <p>We are now ready to help you with your transportation needs! To get started, simply click the button below to log in to your account:</p> 
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'Log in',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'show_img' => 0,
                    'banner_img' => 'profile-bg.jpg',                    
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Register successfully',
                    'push_body' => 'Register successfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Ride Later',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on ride later',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Driver Assigned For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Your Ride Later Trip is Confirmed</p>
                                    <p>Thank You for Riding with us</p>
                                    <p>Your "ride later" trip has been successfully scheduled.</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                     
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'New Trip Requested 😊️',
                    'push_body' => 'New Trip Requested, you can accept or Reject the request',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Invoice For End of the Ride User',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on invoice for the end of the ride',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Invoice For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thank You for Riding with us</p>
                                    <p><strong>Here is the summary of your recent trip: <strong></p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Ended the trip',
                    'push_body' => 'Driver finished the ride, Please help us by rating the driver',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Referral',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on referral code',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Referral Code User',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thanks you! We are excited to offer you a referral code that you can share with your friends,
                                    family, or colleagues.</p>
                                    <p>When they use this referral code, they will receive a discount on their first ride, and you will earn rewards as well.</p>
                                    <p>Share this code with others, and start earning rewards today! The more you refer, the more you can earn!</p>
                                    <p>To use the referral code, simply share it with the person you refer,
                                     and they can enter it during the booking process on our app.</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You have Earned with your Referral code 😊️',
                    'push_body' => 'We are happy to inform you that you have earned money with your referral code',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Wallet Amount',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on wallet amount Adjusted',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Adjusted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are happy to inform you that an amount has been successfully Adjusted to your wallet.</p> 
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',
                    'show_img' => 0,                    
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Amount Added Succesfully',
                    'push_body' => 'Amount Credited to Your Wallet Succesfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Amount Transfer',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on amount transfer',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Transfer',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You Have Received Money',
                    'push_body' => 'You Have Received Money From',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Document Expired',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Document Expired',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Document Expired',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Document Expired, Kindly Update your documents</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Document Expires',
                    'push_body' => 'Document Expired',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Ride Remainder',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Ride Remainder',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Ride Remainder',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>To get a Ride, Open the app</p>
                                     <p>Best regards, </p>         
                                    <p>MI Softwares</p>',

                    'button_name'=>'Share',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Gentle Reminder 😊️',
                    'push_body' => 'Please open the App to get ride requests',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Account Approval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on account approval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Account Approval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Congratulations,{name} </p>
                                    <p>We are  to inform you that your driver account  has been successfully approved. You are now ready to start accepting ride requests and earning..</p>   
                                    <p>Please log in to your account using the credentials provided during registration. If you encounter any issues, feel free to reach out to our support team.</p>        
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name' => 'Button',
                    'button_url' => 'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]), 
                    'push_title' => 'Account Approved 😃️',
                    'push_body' => 'Your profile verified and approved',
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Account Disapproval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on account disapproval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Account Disapproval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p> We regret to inform you that your application to become a driver with our taxi service has not been approved at this time.</p>   
                                    <p>If you have any questions or need further clarification, feel free to contact our support team.</p>        
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name' => 'Button',
                    'button_url' => 'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Account Declined 🙁️',
                    'push_body' => 'Your Account declined due to some reason. please contact our admin', 
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Wallet Amount',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on wallet amount Adjusted',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Wallet Amount Adjusted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are happy to inform you that an amount has been successfully Adjusted to your wallet.</p>  
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Amount Added Succesfully',
                    'push_body' => 'Amount Credited to Your Wallet Succesfully', 
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Withdrawal Request Approval',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on request approval',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Withdrawal Request Approval',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>  Your withdrawal request has been approved. Here are the details:.</p>                                    
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you have any issues with payment, kindly reply to this email or send an email to support@gmail.com</p>
                                    <p>Thank you for using our services!</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg', 
                    'show_img' => 0,                   
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Credited 😃️',
                    'push_body' => 'Your Payment Credited To Your Given Account',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Withdrawal Request Decline',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on request decline',
                    'push_notification' => 1,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Withdrawal Request Decline',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Unfortunately, your withdrawal request has been declined.</p>   
                                    <p>If you have any issues with payment, kindly reply to this email or send an email to support@gmail.com</p>
                                    <p>Thank you for using our services!</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',   
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Declained ',
                    'push_body' => 'Your Payment Declained',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Invoice For End of the Ride Driver',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on invoice for the end of the ride',
                    'push_notification' => 0,
                    'mail' => 1,
                    'sms' => 1,
                    'email_subject' => 'Invoice For Ride',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Thank You for Riding with us</p>
                                    <p><strong>Here is the summary of your recent trip: <strong></p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Invoice Downloaded successfully',
                    'push_body' => 'Invoice Downloaded successfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Trip Cancelled',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Trip Cancelled',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Cancelled',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Cancelled By Customer 🙁️',
                    'push_body' => 'The customer cancelled the ride,please wait for another ride',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Trip Cancelled By System',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Trip Cancelled By System',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Cancelled',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Cancelled',
                    'push_body' => 'Trip Cancelled By System',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Trip Cancelled By Driver',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Trip Cancelled',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Cancelled',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Cancelled By Driver 🙁️',
                    'push_body' => 'The Driver cancelled the ride,please wait for another ride',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Daily Incentive',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Daily Incentive',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Daily Incentive',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Daily Incentive</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Daily Incentive',
                    'push_body' => 'Daily Incentive Credited To Your Wallet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Weekly Incentive',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Weekly Incentive',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Weekly Incentive',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Weekly Incentive</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Weekly Incentive',
                    'push_body' => 'Weekly Incentive Credited To Your Wallet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Approved',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Approved',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Approved',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Trip Cancelled</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Got Approved',
                    'push_body' => 'Fleet Got Approved, Now you can assign driver for your fleet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Decline',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Decline',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Decline',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Fleet Decline</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Got Declined by Admin',
                    'push_body' => 'Fleet Got Declined by Admin, Please Contact Admin',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Fleet Account Removed',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Fleet Account Removed',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Fleet Account Removed',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>Fleet Account Removed</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Fleet Removed From Your Account',
                    'push_body' => 'Fleet Removed From Your Account, Please Wait For Assigning Fleet',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'New Fleet Assigned',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on New Fleet Assigned',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'New Fleet Assigned',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>New Fleet Assigned</p>',
                    'button_name'=>'Reset Password',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',                      
                    'show_img' => 0,                 
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'New Fleet Assigned For you',
                    'push_body' => 'New Fleet assigned for you',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Transfer Credit Points',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Transfer Credit Points',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Transfer Credit Points',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Reward Points Converted 😃️',
                    'push_body' => 'Your Reward Points Credited To Your Account',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Transaction Failed',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Transaction Failed',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Transaction Failed',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Transaction Failed',
                    'push_body' => 'Transaction Failed',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Payment Received',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Payment Received',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Payment Received',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Payment Received',
                    'push_body' => 'Payment Received from customer',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Ride Confirmation',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Ride Confirmation',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Ride Confirmed By Customer',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Ride Confirmed By Customer',
                    'push_body' => 'Ride Confirmed By Customer, Please Reach the customer pickup location on time',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Arrived',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver Arrived',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver Arrived',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Arrived 😊️',
                    'push_body' => 'The Driver arrived to pick you up',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Driver On the way to pickup',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver On the way to pickup',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver On the way to pickup',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Driver Is On The Way To Pickup',
                    'push_body' => 'Driver Is On The Way To Pickup',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Trip Started',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Trip Started',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Started',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Started',
                    'push_body' => 'Trip started towards the drop location',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'User Trip Request Accepted',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Trip Request Accepted',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Trip Request Accepted',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Trip Request Accepted',
                    'push_body' => 'The Driver is coming to pick you',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'Driver not Found',
                    'topics_content' => 'Choose how Customer will get notified about Sent notification on Driver not Found',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Driver not Found',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'No Driver Found Around You 🙁️',
                    'push_body' => 'Sorry plese try again after some times,there is no driver available for your ride now',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Subscription',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver Subscription Successfully',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Subscribed Succesfully',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Subscribed Succesfully',
                    'push_body' => 'You have subscribed successfully',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Diagnostic',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver Diagnostic Successfully',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Subscribed Succesfully',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You are Ready to Take a Ride',
                    'push_body' => 'You are Ready to Take a Ride',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Driver Tips',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on Driver added tips Successfully',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Tips added Succesfully',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>We are writing to confirm that you have successfully transferred an amount from your wallet.</p>
                                    <p><strong>Transaction Details</strong></p>
                                    <p><strong>Transaction Id:</strong> {transaction_id}</p>
                                    <p><strong>Amount:</strong> {currency}{amount}</p>
                                    <p><strong>Current Balance:</strong>{currency}{current_balance}</p>
                                    <p>If you did not initiate this transfer, please contact our support team immediately.</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'You have Earned with your Tips 😊️',
                    'push_body' => 'We are happy to inform you that you have earned money with your Tips',
                    
                ],

                [
                    'id' => Str::uuid(),
                    'role' =>'user',
                    'topics' => 'New Chat Message',
                    'topics_content' => 'Choose how User and Driver will get notified about Sent notification on New Chat is Received',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'New Chat Message',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>New Chat Mesage</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'New Chat Message',
                    'push_body' => 'New Chat Message',
                    
                ],
                [
                    'id' => Str::uuid(),
                    'role' =>'driver',
                    'topics' => 'Change Drop Destination',
                    'topics_content' => 'Choose how Driver will get notified about Sent notification on drop destination has changed',
                    'push_notification' => 1,
                    'mail' => 0,
                    'sms' => 0,
                    'email_subject' => 'Change Drop Destination',
                    'logo_img' => 'logo-light.png',
                    'mail_body' => '
                                    <p>Hello {name}</p>
                                    <p>The drop location has been changed</p>
                                    <p>Thank you for using our services!</p>
                                    <p>Best regards, </p>         
                                    <p>MI Softwares</p>',
                    'button_name'=>'View Details',
                    'button_url'=>'https://play.google.com/store/apps/details?id=tagxi.bidding.user',
                    'show_button' => 0,
                    'banner_img' => 'profile-bg.jpg',    
                    'show_img' => 0,                
                    'footer_content' => '<p>If you have any queries , Please email us support@gmail.com.They will answer the question and help you out.</p>',
                    'footer_copyrights' => '2021 Misoftwares & Rights Reserved',
                    'show_fbicon' => 1,
                    'show_instaicon' => 1,
                    'show_twittericon' => 1,
                    'show_linkedinicon' => 1,
                    'footer' => json_encode([
                        'footer_fblink' => 'https://www.facebook.com/',
                        'footer_instalink' => 'https://www.instagram.com/',
                        'footer_twitterlink' => 'https://x.com/',
                        'footer_linkedinlink' => 'https://in.linkedin.com/'
                    ]),
                    'push_title' => 'Drop has been changed',
                    'push_body' => 'Drop has been changed',
                    
                ],
        ];

            // dd($notification);
        
            foreach ($notification as $channelData) 
            {
                $topic = $channelData['topics'];
                // Check if the notification channel already exists based on a unique attribute (like 'email_subject')
                $notificationChannel = NotificationChannel::where('topics', $topic)->first();

                if(!$notificationChannel){
                

                    $notificationChannel = NotificationChannel::create($channelData);
                    // dd($notificationChannel);
                    $allTranslations = [];
                    // Prepare translation data for the 'en' locale
                    $allTranslations = [
                        'email_subject' => $channelData['email_subject'],
                        'mail_body' => $channelData['mail_body'],
                        'button_name' => $channelData['button_name'],
                        'footer_content' => $channelData['footer_content'],
                        'footer_copyrights' => $channelData['footer_copyrights'],
                        'push_title' => $channelData['push_title'],
                        'push_body' => $channelData['push_body'],
                        'locale' => 'en',
                        'notification_channel_id' => $notificationChannel->id,
                    ];

                    $enExists = $notificationChannel->notificationChannelTranslationWords()
                                ->where('notification_channel_id',$notificationChannel->id)
                                ->where('locale', 'en')
                                ->first();
                    if (!$enExists) {
                        $notificationChannel->notificationChannelTranslationWords()->insert($allTranslations);
                    } 

                    $locales = ['fr', 'es', 'ar']; // Add more locales if needed

                    $translations = [
                        'fr' => [
                            'New Customer Registration' => [
                                'email_subject' => "Courrier d'inscription",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci de vous être inscrit auprès de nous, votre application de taxi de confiance. Votre inscription a réussi et nous sommes ravis de vous compter parmi nous.</p>
                                                <p>Détails de votre compte</p>
                                                <p>E-mail : {email}</p>
                                                <p>Numéro de mobile : {mobile}</p>
                                                <p>Nous sommes maintenant prêts à vous aider avec vos besoins de transport ! Pour commencer, cliquez simplement sur le bouton ci-dessous pour vous connecter à votre compte :</p> 
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Titre de notification en français',
                                'push_body' => 'Corps de notification en français',
                            ],
                            'Change Drop Destination' => [
                                'email_subject' => "Changer la destination de dépôt",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>La destination a été modifiée</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>MI Softwares</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 & Droits réservés',
                                'push_title' => 'Le drop a été modifié',
                                'push_body' => 'La destination de dépôt a été modifiéeyy',
                            ],
                            'Driver Diagnostic' => [
                                'email_subject' => "Courrier d'inscription",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci de vous être inscrit auprès de nous, votre application de taxi de confiance. Votre inscription a réussi et nous sommes ravis de vous compter parmi nous.</p>
                                                <p>Détails de votre compte</p>
                                                <p>E-mail : {email}</p>
                                                <p>Numéro de mobile : {mobile}</p>
                                                <p>Nous sommes maintenant prêts à vous aider avec vos besoins de transport ! Pour commencer, cliquez simplement sur le bouton ci-dessous pour vous connecter à votre compte :</p> 
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Vous êtes prêt à faire un tour',
                                'push_body' => 'Vous êtes prêt à faire un tour',
                            ],
                            'Driver Tips' => [
                                'email_subject' => "Courrier d'inscription",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci de vous être inscrit auprès de nous, votre application de taxi de confiance. Votre inscription a réussi et nous sommes ravis de vous compter parmi nous.</p>
                                                <p>Détails de votre compte</p>
                                                <p>E-mail : {email}</p>
                                                <p>Numéro de mobile : {mobile}</p>
                                                <p>Nous sommes maintenant prêts à vous aider avec vos besoins de transport ! Pour commencer, cliquez simplement sur le bouton ci-dessous pour vous connecter à votre compte :</p> 
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Vous avez gagné avec vos pourboires 😊️',
                                'push_body' => 'Nous sommes heureux de vous informer que vous avez gagné de l argent grâce à vos pourboires',
                            ],
                        
                            'User Amount Transfer' => [
                                'email_subject' => "Courrier d'inscription",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci de vous être inscrit auprès de nous, votre application de taxi de confiance. Votre inscription a réussi et nous sommes ravis de vous compter parmi nous.</p>
                                                <p>Détails de votre compte</p>
                                                <p>E-mail : {email}</p>
                                                <p>Numéro de mobile : {mobile}</p>
                                                <p>Nous sommes maintenant prêts à vous aider avec vos besoins de transport ! Pour commencer, cliquez simplement sur le bouton ci-dessous pour vous connecter à votre compte :</p> 
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Titre de notification en français',
                                'push_body' => 'Corps de notification en français',
                            ],
                        
                            'Driver Account Approval' => [
                                'email_subject' => "Approbation du compte",
                                'mail_body' => '<p>Félicitations, {name}</p>
                                                <p>Nous devons vous informer que votre compte chauffeur a été approuvé avec succès. Vous êtes maintenant prêt à commencer à accepter des demandes de courses et à gagner des revenus.</p>
                                                <p>Veuillez vous connecter à votre compte en utilisant les identifiants fournis lors de l\'inscription. Si vous rencontrez des problèmes, n\'hésitez pas à contacter notre équipe d\'assistance.</p>
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Compte approuvé 😃️',
                                'push_body' => 'Votre profil vérifié et approuvé',
                            ],
                            'Driver Account Disapproval' => [
                                'email_subject' => "Refus du compte",
                                'mail_body' => '<p>Bonjour, {name}</p>
                                                <p>Nous avons le regret de vous informer que votre demande pour devenir chauffeur auprès de notre service de taxi n\'a pas été approuvée pour le moment.</p>
                                                <p>Si vous avez des questions ou avez besoin de précisions supplémentaires, n\'hésitez pas à contacter notre équipe d\'assistance.</p>
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Compte refusé 🙁️',
                                'push_body' => 'Votre compte a été refusé pour une raison quelconque. veuillez contacter notre administrateur',
                            ],
                            'Driver Wallet Amount' => [
                                'email_subject' => "Montant du portefeuille ajusté",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Nous sommes heureux de vous informer qu\'un montant a été ajusté avec succès à votre portefeuille.</p>
                                                <p>Détails de la transaction</p>
                                                <p>Identifiant de transaction: {transaction_id}</p>
                                                <p>Montante : {currency}{amount}</p>
                                                <p>Solde actuel: {currency}{current_balance}</p>
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Montant ajouté avec succès',
                                'push_body' => 'Montant crédité avec succès sur votre portefeuille',
                            ],
                            'User Wallet Amount' => [
                                'email_subject' => "Montant du portefeuille ajusté",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Nous sommes heureux de vous informer qu\'un montant a été ajusté avec succès à votre portefeuille.</p>
                                                <p>Détails de la transaction</p>
                                                <p>Identifiant de transaction: {transaction_id}</p>
                                                <p>Montante : {currency}{amount}</p>
                                                <p>Solde actuel: {currency}{current_balance}</p>
                                                <p>Meilleures salutations, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Montant ajouté avec succès',
                                'push_body' => 'Montant crédité avec succès sur votre portefeuille',
                            ],
                            'User Ride Later' => [
                                'email_subject' => "Chauffeur affecté au trajet",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Votre voyage Ride Later est confirmé</p>
                                                <p>Merci d\'avoir roulé avec nous</p>
                                                <p>Votre voyage « rouler plus tard » a été planifié avec succès.</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Nouveau voyage demandé 😊️',
                                'push_body' => 'Nouveau voyage demandé, vous pouvez accepter ou refuser la demande',
                            ],
                            'User Referral' => [
                                'email_subject' => "Utilisateur du code de référence",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci! Nous sommes ravis de vous proposer un code de parrainage que vous pouvez partager avec vos amis, votre famille ou vos collègues.</p>
                                                <p>Lorsqu\'ils utiliseront ce code de parrainage, ils bénéficieront d\'une réduction sur leur premier trajet et vous gagnerez également des récompenses.</p>
                                                <p>Partagez ce code avec d\'autres et commencez à gagner des récompenses dès aujourd\'hui ! Plus vous parrainez, plus vous pouvez gagner !</p>
                                                <p>Pour utiliser le code de parrainage, partagez-le simplement avec la personne que vous parrainez, et elle pourra le saisir lors du processus de réservation sur notre application.</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Vous avez gagné avec votre code de parrainage 😊️',
                                'push_body' => 'Nous sommes heureux de vous informer que vous avez gagné de l\'argent avec votre code de parrainage',
                            ],
                            'Wallet Amount Transfer' => [
                                'email_subject' => "Transfert du montant du portefeuille",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Nous vous écrivons pour confirmer que vous avez réussi à transférer un montant depuis votre portefeuille.</p>
                                                <p>Détails de la transaction</p>
                                                <p>ID de transaction : {transaction_id}</p>
                                                <p>Montant : {devise}{montant}</p>
                                                <p>Solde actuel : {currency}{current_balance}</p>
                                                <p>Si vous n\'avez pas initié ce transfert, veuillez contacter immédiatement notre équipe d\'assistance.</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Vous avez reçu de l\'argent',
                                'push_body' => 'Vous avez reçu de l\'argent de',
                            ],
                            'Driver Document Expired' => [
                                'email_subject' => "Document expiré",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Document expiré, veuillez mettre à jour vos documents</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Le document expire',
                                'push_body' => 'Document expiré',
                            ],
                            'Driver Ride Remainder' => [
                                'email_subject' => "Document expiré",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Document expiré, veuillez mettre à jour vos documents</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Doux rappel 😊️',
                                'push_body' => 'Veuillez ouvrir l\'application pour recevoir des demandes de trajet',
                            ],
                            'Driver Withdrawal Request Approval' => [
                                'email_subject' => "Approbation de la demande de retrait",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Détails de la transaction</p>
                                                <p>ID de transaction : {transaction_id}</p>
                                                <p>Montant:{currency}{amount}</p>
                                                <p>Solde actuel: {currency}{current_balance}</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Paiement crédité 😃️',
                                'push_body' => 'Votre paiement crédité sur votre compte donné',
                            ],
                            'Driver Withdrawal Request Decline' => [
                                'email_subject' => "Refus de la demande de retrait",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Paiement refusé',
                                'push_body' => 'Votre paiement refusé',
                            ],
                            'Driver Subscription' => [
                                'email_subject' => "Abonnement du pilote",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Abonné avec succès',
                                'push_body' => 'Vous vous êtes abonné avec succès',
                            ],

                            'Driver not Found' => [
                                'email_subject' => "Pilote introuvable",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Aucun chauffeur trouvé autour de vous 🙁️',
                                'push_body' => 'Désolé, veuillez réessayer après quelques instants. Aucun chauffeur n\'est disponible pour votre trajet pour le moment.',
                            ],
                            'User Trip Request Accepted' => [
                                'email_subject' => "Demande de voyage acceptée",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Demande de voyage acceptée',
                                'push_body' => 'Le Chauffeur vient vous chercher',
                            ],
                            'User Trip Started' => [
                                'email_subject' => "Voyage commencé",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Voyage commencé',
                                'push_body' => 'Le voyage a commencé vers le lieu de dépôt',
                            ],
                            'Driver On the way to pickup' => [
                                'email_subject' => "Le conducteur est en route vers le ramassage",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Le conducteur est en route vers le ramassage',
                                'push_body' => 'Le conducteur est en route vers le ramassage',
                            ],
                            'Driver Arrived' => [
                                'email_subject' => "Chauffeur arrivé",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Chauffeur arrivé',
                                'push_body' => 'Chauffeur arrivé',
                            ],
                            'Driver Ride Confirmation' => [
                                'email_subject' => "Trajet confirmé par le client",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Trajet confirmé par le client',
                                'push_body' => 'Trajet confirmé par le client, veuillez atteindre le lieu de prise en charge du client à temps',
                            ],
                            'Driver Payment Received' => [
                                'email_subject' => "Paiement reçu",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Paiement reçu',
                                'push_body' => 'Paiement reçu du client',
                            ],
                            'User Transaction Failed' => [
                                'email_subject' => "Échec de la transaction",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Échec de la transaction',
                                'push_body' => 'Échec de la transaction',
                            ],
                            'User Transfer Credit Points' => [
                                'email_subject' => "Points de récompense convertis",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Points de récompense convertis 😃️',
                                'push_body' => 'Vos points de récompense crédités sur votre compte',
                            ],
                            'New Fleet Assigned' => [
                                'email_subject' => "Nouvelle flotte attribuée pour vous",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Nouvelle flotte attribuée pour vous',
                                'push_body' => 'Nouvelle flotte attribuée pour vous',
                            ],
                            'Fleet Account Removed' => [
                                'email_subject' => "Flotte supprimée de votre compte",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Flotte supprimée de votre compte',
                                'push_body' => 'Flotte supprimée de votre compte, veuillez attendre l\'attribution de la flotte',
                            ],
                            'Fleet Decline' => [
                                'email_subject' => "La flotte a été refusée par l'administrateur",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'La flotte a été refusée par l\'administrateur',
                                'push_body' => 'La flotte a été refusée par l\'administrateur, veuillez contacter l\'administrateur',
                            ],
                            'Fleet Approved' => [
                                'email_subject' => "La flotte a été approuvée",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'La flotte a été approuvée',
                                'push_body' => 'La flotte a été approuvée, vous pouvez désormais attribuer un chauffeur à votre flotte',
                            ],
                            'Driver Daily Incentive' => [
                                'email_subject' => "Incitation quotidienne",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Incitation quotidienne',
                                'push_body' => 'Incitation quotidienne créditée sur votre portefeuille',
                            ],
                            'Driver Weekly Incentive' => [
                                'email_subject' => "Incitatif hebdomadaire",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Incitatif hebdomadaire',
                                'push_body' => 'Incitation hebdomadaire créditée sur votre portefeuille',
                            ],
                            'Trip Cancelled By Driver' => [
                                'email_subject' => "Voyage annulé par le chauffeur",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Voyage annulé par le chauffeur 🙁️',
                                'push_body' => 'Le chauffeur a annulé le trajet, veuillez attendre un autre trajet',
                            ],
                            'Trip Cancelled' => [
                                'email_subject' => "Voyage annulé par le client",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Voyage annulé par le client',
                                'push_body' => 'Le client a annulé le trajet, veuillez attendre un autre trajet',
                            ],
                            'Invoice For End of the Ride User' => [
                                'email_subject' => "Facture pour le trajet",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci d\'avoir roulé avec nous</p>
                                                <p>Voici le résumé de votre récent voyage :</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Chauffeur A terminé le voyage',
                                'push_body' => 'Le chauffeur a terminé le trajet. Aidez-nous en évaluant le chauffeur.',
                            ],
                            'Invoice For End of the Ride Driver' => [
                                'email_subject' => "Facture pour le trajet",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Merci d\'avoir roulé avec nous</p>
                                                <p>Voici le résumé de votre récent voyage :</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Chauffeur A terminé le voyage',
                                'push_body' => 'Le chauffeur a terminé le trajet. Aidez-nous en évaluant le chauffeur.',
                            ],
                            'Trip Cancelled By System' => [
                                'email_subject' => "Voyage annulé",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Malheureusement, votre demande de retrait a été refusée.</p>
                                                <p>Si vous rencontrez des problèmes avec le paiement, veuillez répondre à cet e-mail ou envoyer un e-mail à support@gmail.com</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Voyage annulé',
                                'push_body' => 'Voyage annulé par le système',
                            ],
                            'New Chat Message' => [
                                'email_subject' => "Nouveau message de discussion",
                                'mail_body' => '<p>Bonjour {name}</p>
                                                <p>Nouveau message de discussion</p>
                                                <p>Merci d\'utiliser nos services !</p>
                                                <p>Cordialement, </p>         
                                                <p>Logiciels MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si vous avez des questions, veuillez nous envoyer un e-mail à support@gmail.com. Ils répondront à la question et vous aideront.</p>',
                                'footer_copyrights' => 'Misoftwares 2021 et droits réservés',
                                'push_title' => 'Nouveau message de discussion',
                                'push_body' => 'Nouveau message de discussion',
                            ],
                            
                        ],
                        'es' => [
                            'New Chat Message' => [
                                'email_subject' => 'Nuevo mensaje de chat',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Nuevo mensaje de chat</p> 
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Nuevo mensaje de chat',
                                'push_body' => 'Nuevo mensaje de chat',
                            ],
                            'New Customer Registration' => [
                                'email_subject' => 'Correo de registro',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por registrarte con nosotros, tu aplicación de taxis de confianza. Su registro fue exitoso y estamos emocionados de tenerlo a bordo.</p>
                                                <p>Detalles de su cuenta</p>
                                                <p>Correo electrónico: {email}</p>
                                                <p>Número de móvil: {mobile}</p>
                                                <p>¡Ahora estamos listos para ayudarle con sus necesidades de transporte! Para comenzar, simplemente haga clic en el botón a continuación para iniciar sesión en su cuenta:</p> 
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Título de la notificación en español',
                                'push_body' => 'Cuerpo de la notificación en español',
                            ],
                            'Change Drop Destination' => [
                                'email_subject' => 'Cambiar destino de entrega',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>El destino ha cambiado</p>
                                                <p>¡Gracias por usar nuestros servicios!</p>
                                                <p>Atentamente,</p> 
                                                <p>MI Softwares</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna pregunta, envíenos un correo electrónico a support@gmail.com. Le responderemos y le ayudaremos.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El destino de descarga ha cambiado',
                                'push_body' => 'El destino de descarga ha cambiado',
                            ],
                            'Driver Diagnostic' => [
                                'email_subject' => 'Correo de registro',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por registrarte con nosotros, tu aplicación de taxis de confianza. Su registro fue exitoso y estamos emocionados de tenerlo a bordo.</p>
                                                <p>Detalles de su cuenta</p>
                                                <p>Correo electrónico: {email}</p>
                                                <p>Número de móvil: {mobile}</p>
                                                <p>¡Ahora estamos listos para ayudarle con sus necesidades de transporte! Para comenzar, simplemente haga clic en el botón a continuación para iniciar sesión en su cuenta:</p> 
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Estás listo para dar un paseo',
                                'push_body' => 'Estás listo para dar un paseo',
                            ],
                            'Driver Tips' => [
                                'email_subject' => 'Correo de registro',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por registrarte con nosotros, tu aplicación de taxis de confianza. Su registro fue exitoso y estamos emocionados de tenerlo a bordo.</p>
                                                <p>Detalles de su cuenta</p>
                                                <p>Correo electrónico: {email}</p>
                                                <p>Número de móvil: {mobile}</p>
                                                <p>¡Ahora estamos listos para ayudarle con sus necesidades de transporte! Para comenzar, simplemente haga clic en el botón a continuación para iniciar sesión en su cuenta:</p> 
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Has Ganado con tus Propinas 😊️',
                                'push_body' => 'Nos alegra informarte que has ganado dinero con tus Tips',
                            ],
                            'User Amount Transfer' => [
                                'email_subject' => 'Correo de registro',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por registrarte con nosotros, tu aplicación de taxis de confianza. Su registro fue exitoso y estamos emocionados de tenerlo a bordo.</p>
                                                <p>Detalles de su cuenta</p>
                                                <p>Correo electrónico: {email}</p>
                                                <p>Número de móvil: {mobile}</p>
                                                <p>¡Ahora estamos listos para ayudarle con sus necesidades de transporte! Para comenzar, simplemente haga clic en el botón a continuación para iniciar sesión en su cuenta:</p> 
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Título de la notificación en español',
                                'push_body' => 'Cuerpo de la notificación en español',
                            ],
                        
                            'Driver Account Approval' => [
                                'email_subject' => "Aprobación de cuenta",
                                'mail_body' => '<p>Felicidades, {name}</p>
                                                <p>Le informamos que su cuenta de conductor ha sido aprobada exitosamente. Ahora está listo para comenzar a aceptar solicitudes de viaje y ganar dinero.</p>
                                                <p>Inicie sesión en su cuenta utilizando las credenciales proporcionadas durante el registro. Si tiene algún problema, no dude en comunicarse con nuestro equipo de soporte.</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Cuenta aprobada 😃️',
                                'push_body' => 'Tu perfil verificado y aprobado',
                            ],
                            'Driver Account Disapproval' => [
                                'email_subject' => "Rechazo de cuenta",
                                'mail_body' => '<p>Hola, {name}</p>
                                                <p>Lamentamos informarle que su solicitud para convertirse en conductor de nuestro servicio de taxi no ha sido aprobada en este momento.</p>
                                                <p>Si tiene alguna pregunta o necesita más aclaraciones, no dude en ponerse en contacto con nuestro equipo de soporte.</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Bouton',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Cuenta rechazada 🙁️',
                                'push_body' => 'Su cuenta fue rechazada por algún motivo. por favor contacte a nuestro administrador',
                            ],
                            'Driver Wallet Amount' => [
                                'email_subject' => 'Monto de billetera ajustado',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Nos complace informarle que un monto se ha ajustado exitosamente a su billetera.</p>
                                                <p>Detalles de la transacción</p>
                                                <p>ID de transacción: {transaction_id}</p>
                                                <p>Cantidad: {currency}{amount}</p>
                                                <p>Saldo actual: {currency}{current_balance}</p>
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Cantidad agregada exitosamente',
                                'push_body' => 'Monto acreditado en su billetera con éxito',
                            ],
                            'User Wallet Amount' => [
                                'email_subject' => 'Monto de billetera ajustado',
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Nos complace informarle que un monto se ha ajustado exitosamente a su billetera.</p>
                                                <p>Detalles de la transacción</p>
                                                <p>ID de transacción: {transaction_id}</p>
                                                <p>Cantidad: {currency}{amount}</p>
                                                <p>Saldo actual: {currency}{current_balance}</p>
                                                <p>Saludos cordiales,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Cantidad agregada exitosamente',
                                'push_body' => 'Monto acreditado en su billetera con éxito',
                            ],
                            'User Ride Later' => [
                                'email_subject' => "Conductor asignado para el viaje",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Su viaje de viaje más tarde está confirmado</p>
                                                <p>Gracias por viajar con nosotros</p>
                                                <p>Su viaje "viajar más tarde" se ha programado correctamente.</p>
                                                <p>Atentamente, </p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Nuevo viaje solicitado 😊️',
                                'push_body' => 'Nuevo Viaje Solicitado, puedes aceptar o Rechazar la solicitud',
                            ],
                            'User Referral' => [
                                'email_subject' => "Usuario del código de referencia",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>¡Gracias! Nos complace ofrecerle un código de referencia que puede compartir con sus amigos, familiares o colegas.</p>
                                                <p>Cuando utilicen este código de referencia, recibirán un descuento en su primer viaje y usted también obtendrá recompensas.</p>
                                                <p>¡Comparta este código con otras personas y comience a ganar recompensas hoy! ¡Cuanto más recomiendes, más podrás ganar!</p>
                                                <p>Para usar el código de referencia, simplemente compártelo con la persona que recomiendas y podrá ingresarlo durante el proceso de reserva en nuestra aplicación.</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Has ganado con tu código de referencia 😊️',
                                'push_body' => 'Nos complace informarle que ha ganado dinero con su código de referencia.',
                            ],
                            'Wallet Amount Transfer' => [
                                'email_subject' => "Transferencia de monto de billetera",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Le escribimos para confirmar que ha transferido exitosamente una cantidad desde su billetera.</p>
                                                <p>Detalles de la transacción</p>
                                                <p>ID de transacción: {transaction_id}</p>
                                                <p>Monto: {currency}{amount}</p>
                                                <p>Saldo actual:{currency}{current_balance}</p>
                                                <p>Si no inició esta transferencia, comuníquese con nuestro equipo de soporte de inmediato.</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Has recibido dinero',
                                'push_body' => 'Has recibido dinero de',
                            ],
                            'Driver Document Expired' => [
                                'email_subject' => "Documento caducado",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Documento vencido, por favor actualice sus documentos</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El documento vence',
                                'push_body' => 'Documento caducado',
                            ],
                            'Driver Ride Remainder' => [
                                'email_subject' => "Documento caducado",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Documento vencido, por favor actualice sus documentos</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Recordatorio gentil 😊️',
                                'push_body' => 'Abra la aplicación para recibir solicitudes de viaje.',
                            ],
                            'Driver Withdrawal Request Approval' => [
                                'email_subject' => "Aprobación de solicitud de retiro",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Su solicitud de retiro ha sido aprobada. Aquí están los detalles:.</p>
                                                <p>Detalles de la transacción</p>
                                                <p>ID de transacción: {transaction_id}</p>
                                                <p>Monto: {currency}{amount}</p>
                                                <p>Saldo actual:{currency}{current_balance}</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Pago acreditado 😃️',
                                'push_body' => 'Su pago acreditado a su cuenta dada',
                            ],
                            'Driver Withdrawal Request Decline' => [
                                'email_subject' => "Rechazo de solicitud de retiro",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Pago rechazado',
                                'push_body' => 'Su pago rechazado',
                            ],
                            'Driver Subscription' => [
                                'email_subject' => "Suscripción de conductor",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Suscrito con éxito',
                                'push_body' => 'Te has suscrito exitosamente',
                            ],

                            'Driver not Found' => [
                                'email_subject' => "Controlador no encontrado",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'No se encontró ningún conductor a tu alrededor 🙁️',
                                'push_body' => 'Lo sentimos, inténtelo de nuevo después de algunas veces. No hay ningún conductor disponible para su viaje en este momento.',
                            ],
                            'User Trip Request Accepted' => [
                                'email_subject' => "Solicitud de viaje aceptada",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Solicitud de viaje aceptada',
                                'push_body' => 'El conductor viene a recogerte.',
                            ],
                            'User Trip Started' => [
                                'email_subject' => "Viaje iniciado",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Viaje iniciado',
                                'push_body' => 'El viaje comenzó hacia el lugar de entrega.',
                            ],
                            'Driver On the way to pickup' => [
                                'email_subject' => "La conductora está en camino de recogida",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'La conductora está en camino de recogida',
                                'push_body' => 'La conductora está en camino de recogida',
                            ],
                            'Driver Arrived' => [
                                'email_subject' => "El conductor llegó",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El conductor llegó',
                                'push_body' => 'El conductor llegó',
                            ],
                            'Driver Ride Confirmation' => [
                                'email_subject' => "Viaje confirmado por el cliente",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Viaje confirmado por el cliente',
                                'push_body' => 'Viaje confirmado por el cliente. Llegue al lugar de recogida del cliente a tiempo.',
                            ],
                            'Driver Payment Received' => [
                                'email_subject' => "Pago recibido",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Pago recibido',
                                'push_body' => 'Pago recibido del cliente',
                            ],
                            'User Transaction Failed' => [
                                'email_subject' => "Transacción fallida",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Transacción fallida',
                                'push_body' => 'Transacción fallida',
                            ],
                            'User Transfer Credit Points' => [
                                'email_subject' => "Puntos de recompensa convertidos",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Puntos de recompensa convertidos 😃️',
                                'push_body' => 'Sus puntos de recompensa acreditados en su cuenta',
                            ],
                            'New Fleet Assigned' => [
                                'email_subject' => "Nueva Flota asignada para ti",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Nueva Flota asignada para ti',
                                'push_body' => 'Nueva Flota asignada para ti',
                            ],
                            'Fleet Account Removed' => [
                                'email_subject' => "Flota eliminada de su cuenta",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Flota eliminada de su cuenta',
                                'push_body' => 'Flota eliminada de su cuenta; espere a que se asigne la flota',
                            ],
                            'Fleet Decline' => [
                                'email_subject' => "El administrador rechazó la flota",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El administrador rechazó la flota',
                                'push_body' => 'El administrador rechazó la flota; comuníquese con el administrador',
                            ],
                            'Fleet Approved' => [
                                'email_subject' => "La flota fue aprobada",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'La flota fue aprobada',
                                'push_body' => 'La flota fue aprobada, ahora puede asignar un conductor para su flota',
                            ],
                            'Driver Daily Incentive' => [
                                'email_subject' => "Incentivo diario",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Incentivo diario',
                                'push_body' => 'Incentivo diario acreditado en su billetera',
                            ],
                            'Driver Weekly Incentive' => [
                                'email_subject' => "Incentivo Semanal",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Incentivo Semanal',
                                'push_body' => 'Incentivo semanal acreditado en su billetera',
                            ],
                            'Trip Cancelled By Driver' => [
                                'email_subject' => "Viaje cancelado por conductor",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Viaje cancelado por conductor',
                                'push_body' => 'El conductor canceló el viaje; espere otro viaje.',
                            ],
                            'Trip Cancelled' => [
                                'email_subject' => "Viaje cancelado por cliente",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Viaje cancelado por cliente 🙁️',
                                'push_body' => 'El cliente canceló el viaje, espere otro viaje.',
                            ],
                            'Invoice For End of the Ride User' => [
                                'email_subject' => "Factura por viaje",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por viajar con nosotros</p>
                                                <p>Aquí está el resumen de su reciente viaje</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El conductor finalizó el viaje.',
                                'push_body' => 'El conductor terminó el viaje. Ayúdenos calificando al conductor.',
                            ],
                            'Invoice For End of the Ride Driver' => [
                                'email_subject' => "Factura por viaje",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Gracias por viajar con nosotros</p>
                                                <p>Aquí está el resumen de su reciente viaje</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'El conductor finalizó el viaje.',
                                'push_body' => 'El conductor terminó el viaje. Ayúdenos calificando al conductor.',
                            ],
                            'Trip Cancelled By System' => [
                                'email_subject' => "Viaje cancelado por cliente",
                                'mail_body' => '<p>Hola {name}</p>
                                                <p>Lamentablemente, su solicitud de retiro ha sido rechazada.</p>
                                                <p>Si tiene algún problema con el pago, responda amablemente a este correo electrónico o envíe un correo electrónico a support@gmail.com</p>
                                                <p>¡Gracias por utilizar nuestros servicios!</p>
                                                <p>Atentamente,</p>         
                                                <p>Software MI</p>',
                                'button_name' => 'Botón',
                                'footer_content' => '<p>Si tiene alguna consulta, envíenos un correo electrónico a support@gmail.com. Ellos responderán la pregunta y lo ayudarán.</p>',
                                'footer_copyrights' => '2021 Missoftwares y derechos reservados',
                                'push_title' => 'Viaje cancelado',
                                'push_body' => 'Viaje cancelado por sistema',
                            ],
                        ],
                        'ar' => [
                            'New Customer Registration' => [
                                'email_subject' => 'بريد التسجيل',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'Benachrichtigungstitel auf Deutsch',
                                'push_body' => 'Benachrichtigungstext auf Deutsch',
                            ],
                            'Change Drop Destination' => [
                                'email_subject' => "تغيير وجهة الإسقاط للسائق",
                                'mail_body' => '<p>مرحبًا {name}
                                                <p>تم تغيير الوجهة</p>
                                                <p>شكرًا لاستخدامك خدماتنا!</p>
                                                <p>مع أطيب التحيات،</p>
                                                <p>MI Softwares</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أي استفسارات، يُرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. سنجيب على استفسارك ونساعدك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم تغيير وجهة الإسقاط',
                                'push_body' => 'تم تغيير وجهة الإسقاط',
                            ],
                            'New Chat Message' => [
                                'email_subject' => 'رسالة دردشة جديدة',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'رسالة دردشة جديدة',
                                'push_body' => 'رسالة دردشة جديدة',
                            ],
                            'Driver Diagnostic' => [
                                'email_subject' => 'بريد التسجيل',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'أنت جاهز للقيام بالرحلة',
                                'push_body' => 'أنت جاهز للقيام بالرحلة',
                            ],
                            'Driver Tips' => [
                                'email_subject' => 'بريد التسجيل',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'لقد ربحت بنصائحك 😊️',
                                'push_body' => 'يسعدنا أن نعلمك أنك كسبت المال من خلال النصائح الخاصة بك',
                            ],
                            'User Amount Transfer' => [
                                'email_subject' => 'بريد التسجيل',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'Benachrichtigungstitel auf Deutsch',
                                'push_body' => 'Benachrichtigungstext auf Deutsch',
                            ],
                            'Driver Withdrawal Request Decline' => [
                                'email_subject' => 'بريد التسجيل',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>نشكرك على الاشتراك معنا، تطبيق سيارات الأجرة الموثوق به. تم تسجيلك بنجاح، ونحن متحمسون لانضمامك إلينا.</p>
                                                <p>تفاصيل حسابك</p>
                                                <p>البريد الإلكتروني: {email}</p>
                                                <p>رقم الجوال: {mobile}</p>
                                                <p>نحن الآن على استعداد لمساعدتك في تلبية احتياجات النقل الخاصة بك! للبدء، ما عليك سوى النقر على الزر أدناه لتسجيل الدخول إلى حسابك:</p> 
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'Benachrichtigungstitel auf Deutsch',
                                'push_body' => 'Benachrichtigungstext auf Deutsch',
                            ],
                            
                            'Driver Account Approval' => [
                                'email_subject' => "تقدير الحساب",
                                'mail_body' => '<p>تهنئة, {name}</p>
                                                <p>نود أن نعلمك أنه تمت الموافقة على حساب السائق الخاص بك بنجاح. أنت الآن جاهز للبدء في قبول طلبات الرحلات وتحقيق الربح..</p>
                                                <p>يرجى تسجيل الدخول إلى حسابك باستخدام بيانات الاعتماد المقدمة أثناء التسجيل. إذا واجهت أي مشاكل، فلا تتردد في التواصل مع فريق الدعم لدينا.</p>
                                                <p>أطيب التحيات, </p>         
                                                <p>برامج إم آي</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تمت الموافقة على الحساب 😃️',
                                'push_body' => 'تم التحقق من ملفك الشخصي والموافقة عليه',
                            ],
                            'Driver Wallet Amount' => [
                                'email_subject' => 'تم تعديل مبلغ المحفظة',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>يسعدنا أن نعلمك أنه تم بنجاح تعديل المبلغ إلى محفظتك.</p>
                                                <p>تفاصيل المعاملة</p>
                                                <p>معرف المعاملة: {transaction_id}</p>
                                                <p>كمية: {currency}{amount}</p>
                                                <p>الرصيد الحالي: {currency}{current_balance}</p>
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تمت إضافة المبلغ بنجاح',
                                'push_body' => 'تم إضافة المبلغ إلى محفظتك بنجاح',
                            ],
                            'User Wallet Amount' => [
                                'email_subject' => 'تم تعديل مبلغ المحفظة',
                                'mail_body' => '<p>{مرحبًا {الاسم</p>
                                                <p>يسعدنا أن نعلمك أنه تم بنجاح تعديل المبلغ إلى محفظتك.</p>
                                                <p>تفاصيل المعاملة</p>
                                                <p>معرف المعاملة: {transaction_id}</p>
                                                <p>كمية: {currency}{amount}</p>
                                                <p>الرصيد الحالي: {currency}{current_balance}</p>
                                                <p>مع أطيب التحيات</p>         
                                                ',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تمت إضافة المبلغ بنجاح',
                                'push_body' => 'تم إضافة المبلغ إلى محفظتك بنجاح',
                            ],
                            'User Ride Later' => [
                                'email_subject' => "السائق المخصص للركوب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>تم تأكيد رحلتك اللاحقة</p>
                                                <p>شكرا لركوبك معنا</p>
                                                <p>تمت جدولة رحلتك "الركوب لاحقًا" بنجاح.</p>
                                                <p>أطيب التحيات, </p>         
                                                <p>برامج إم آي</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'مطلوب رحلة جديدة 😊️',
                                'push_body' => 'تم طلب رحلة جديدة، يمكنك قبول الطلب أو رفضه',
                            ],
                            'User Referral' => [
                                'email_subject' => "مستخدم رمز الإحالة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>يشكركم! يسعدنا أن نقدم لك رمز الإحالة الذي يمكنك مشاركته مع أصدقائك أو عائلتك أو زملائك.</p>
                                                <p>عندما يستخدمون رمز الإحالة هذا، سيحصلون على خصم على رحلتهم الأولى، وستكسب أنت أيضًا مكافآت.</p>
                                                <p>شارك هذا الرمز مع الآخرين وابدأ في كسب المكافآت اليوم! كلما قمت بالإحالة أكثر، كلما زادت أرباحك!</p>
                                                <p>لاستخدام رمز الإحالة، ما عليك سوى مشاركته مع الشخص الذي تحيله، ويمكنه إدخاله أثناء عملية الحجز على تطبيقنا.</p>
                                                <p>أطيب التحيات، </p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'لقد ربحت باستخدام رمز الإحالة الخاص بك 😊️',
                                'push_body' => 'يسعدنا أن نعلمك أنك ربحت المال من خلال رمز الإحالة الخاص بك',
                            ],
                            'Wallet Amount Transfer' => [
                                'email_subject' => "تحويل مبلغ المحفظة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>نكتب إليك لتأكيد أنك قمت بتحويل مبلغ من محفظتك بنجاح.</p>
                                                <p>تفاصيل المعاملة</p>
                                                <p>معرف المعاملة: {transaction_id}</p>
                                                <p>المبلغ: {currency}{amount}</p>
                                                <p>الرصيد الحالي:{currency}{current_balance}</p>
                                                <p>إذا لم تكن أنت من بدأ عملية النقل هذه، فيرجى الاتصال بفريق الدعم لدينا على الفور.</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'لقد تلقيت المال',
                                'push_body' => 'لقد تلقيت المال من,'
                            ],
                            'Driver Document Expired' => [
                                'email_subject' => "انتهت صلاحية الوثيقة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>انتهت صلاحية الوثيقة، يرجى تحديث المستندات الخاصة بك</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'انتهاء صلاحية المستند',
                                'push_body' => 'انتهت صلاحية الوثيقة'
                            ],
                            'Driver Ride Remainder' => [
                                'email_subject' => "انتهت صلاحية الوثيقة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>انتهت صلاحية الوثيقة، يرجى تحديث المستندات الخاصة بك</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تذكير لطيف 😊️',
                                'push_body' => 'يرجى فتح التطبيق للحصول على طلبات الركوب'
                            ],
                            'Driver Account Disapproval' => [
                                'email_subject' => "رفض الحساب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <pنأسف لإبلاغك بأن طلبك لتصبح سائقًا مع خدمة سيارات الأجرة لدينا لم تتم الموافقة عليه في الوقت الحالي.</p>
                                                <p>إذا كان لديك أي أسئلة أو كنت بحاجة إلى مزيد من التوضيح، فلا تتردد في الاتصال بفريق الدعم لدينا.</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'الحساب مرفوض 🙁️',
                                'push_body' => 'لقد تم رفض حسابك لسبب ما. يرجى الاتصال المشرف لدينا'
                            ],
                            'Driver Withdrawal Request Approval' => [
                                'email_subject' => "الموافقة على طلب السحب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>لقد تمت الموافقة على طلب السحب الخاص بك. وإليكم التفاصيل:.</p>
                                                <p>تفاصيل المعاملة</p>
                                                <p>معرف المعاملة: {transaction_id}</p>
                                                <p>المبلغ: {currency}{amount}</p>
                                                <p>الرصيد الحالي:{currency}{current_balance}</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'الدفع مضاف 😃️',
                                'push_body' => 'تم إضافة دفعتك إلى حسابك المحدد'
                            ],
                            'Driver Withdrawal Request Approval' => [
                                'email_subject' => "رفض طلب السحب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم رفض الدفع',
                                'push_body' => 'تم رفض الدفع الخاص بك'
                            ],
                            'Driver Subscription' => [
                                'email_subject' => "اشتراك السائق",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم الاشتراك بنجاح',
                                'push_body' => 'لقد قمت بالاشتراك بنجاح'
                            ],
                            
                            'Driver not Found' => [
                                'email_subject' => "لم يتم العثور على برنامج التشغيل",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'لم يتم العثور على سائق من حولك 🙁️',
                                'push_body' => 'عذرًا، يرجى المحاولة مرة أخرى بعد مرور بعض الوقت، لا يوجد سائق متاح لرحلتك الآن'
                            ],
                            'User Trip Request Accepted' => [
                                'email_subject' => "لم يتم العثور على برنامج التشغيل",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم قبول طلب الرحلة',
                                'push_body' => 'السائق قادم لاصطحابك'
                            ],
                            'User Trip Started' => [
                                'email_subject' => "بدأت الرحلة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'بدأت الرحلة',
                                'push_body' => 'بدأت الرحلة نحو موقع الهبوط'
                            ],
                            'Driver On the way to pickup' => [
                                'email_subject' => "السائق في طريقه للالتقاط",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'السائق في طريقه للالتقاط',
                                'push_body' => 'السائق في طريقه للالتقاط'
                            ],
                            'Driver Arrived' => [
                                'email_subject' => "وصل السائق",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'وصل السائق',
                                'push_body' => 'وصل السائق'
                            ],
                            'Driver Ride Confirmation' => [
                                'email_subject' => "تم تأكيد الرحلة من قبل العميل",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم تأكيد الرحلة من قبل العميل',
                                'push_body' => 'تم تأكيد الرحلة من قبل العميل، يرجى الوصول إلى موقع الالتقاء بالعميل في الوقت المحدد'
                            ],
                            'Driver Payment Received' => [
                                'email_subject' => "تم استلام الدفع",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم استلام الدفع',
                                'push_body' => 'الدفع المستلم من العميل'
                            ],
                            'User Transaction Failed' => [
                                'email_subject' => "فشلت الصفقة",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'فشلت الصفقة',
                                'push_body' => 'فشلت الصفقة'
                            ],
                            'User Transfer Credit Points' => [
                                'email_subject' => "تم تحويل نقاط المكافأة ",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم تحويل نقاط المكافأة 😃️',
                                'push_body' => 'نقاط المكافأة الخاصة بك تضاف إلى حسابك',
                            ],
                            'New Fleet Assigned' => [
                                'email_subject' => "أسطول جديد مخصص لك",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'أسطول جديد مخصص لك',
                                'push_body' => 'أسطول جديد مخصص لك',
                            ],
                            'Fleet Account Removed' => [
                                'email_subject' => "أتمت إزالة الأسطول من حسابك",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تمت إزالة الأسطول من حسابك',
                                'push_body' => 'تمت إزالة الأسطول من حسابك، يرجى الانتظار حتى يتم تعيين الأسطول',
                            ],
                            'Fleet Decline' => [
                                'email_subject' => "تم رفض الأسطول من قبل المشرف",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم رفض الأسطول من قبل المشرف',
                                'push_body' => 'تم رفض الأسطول من قبل المشرف، يرجى الاتصال بالمسؤول',
                            ],
                            'Fleet Approved' => [
                                'email_subject' => "تمت الموافقة على الأسطول",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تمت الموافقة على الأسطول',
                                'push_body' => 'تمت الموافقة على الأسطول، الآن يمكنك تعيين سائق لأسطولك',
                            ],
                            'Driver Daily Incentive' => [
                                'email_subject' => "الحافز اليومي",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'الحافز اليومي',
                                'push_body' => 'الحوافز اليومية تضاف إلى محفظتك',
                            ],
                            'Driver Weekly Incentive' => [
                                'email_subject' => "الحافز الأسبوعي",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'الحافز الأسبوعي',
                                'push_body' => 'حافز أسبوعي يُضاف إلى محفظتك',
                            ],
                            'Trip Cancelled By Driver' => [
                                'email_subject' => "تم إلغاء الرحلة من قبل العميل ",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم إلغاء الرحلة من قبل العميل 🙁️',
                                'push_body' => 'ألغى السائق الرحلة، يرجى الانتظار لرحلة أخرى',
                            ],
                            'Trip Cancelled' => [
                                'email_subject' => "تم إلغاء الرحلة من قبل العميل ",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم إلغاء الرحلة من قبل العميل 🙁️',
                                'push_body' => 'ألغى العميل الرحلة، برجاء الانتظار لرحلة أخرى',
                            ],
                            'Invoice For End of the Ride User' => [
                                'email_subject' => "فاتورة للركوب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>شكرا لركوبك معنا</p>
                                                <p>فيما يلي ملخص رحلتك الأخيرة:</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'أنهى السائق الرحلة',
                                'push_body' => 'أنهى السائق الرحلة، الرجاء مساعدتنا بتقييم السائق',
                            ],
                            'Invoice For End of the Ride Driver' => [
                                'email_subject' => "فاتورة للركوب",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>شكرا لركوبك معنا</p>
                                                <p>فيما يلي ملخص رحلتك الأخيرة:</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'أنهى السائق الرحلة',
                                'push_body' => 'أنهى السائق الرحلة، الرجاء مساعدتنا بتقييم السائق',
                            ],
                            'Trip Cancelled By System' => [
                                'email_subject' => "تم إلغاء الرحلة من قبل العميل ",
                                'mail_body' => '<p>مرحبًا {name}</p>
                                                <p>للأسف، تم رفض طلب السحب الخاص بك.</p>
                                                <p>إذا كانت لديك أية مشكلات تتعلق بالدفع، فيرجى الرد على هذا البريد الإلكتروني أو إرسال بريد إلكتروني إلى support@gmail.com</p>
                                                <p>شكرا لك على استخدام خدماتنا!</p>
                                                <p>أطيب التحيات،</p>         
                                                <p>برامج MI</p>',
                                'button_name' => 'زر',
                                'footer_content' => '<p>إذا كانت لديك أية استفسارات، يرجى مراسلتنا عبر البريد الإلكتروني support@gmail.com. وسوف يجيبون على السؤال ويساعدونك.</p>',
                                'footer_copyrights' => '2021 برامج Misoftware والحقوق محفوظة',
                                'push_title' => 'تم إلغاء الرحلة',
                                'push_body' => 'تم إلغاء الرحلة بواسطة النظام',
                            ],
                        ],
                    ];
                
                    // dd($channelData['id']);
                
                    foreach ($locales as $locale) {
                        // foreach ($channelData as $topic) {

                            
                            if (!isset($translations[$locale][$topic]) || !is_array($translations[$locale][$topic])) {
                            // if(!$translations[$locale][$topic]){
                                $translations[$locale][$topic] = []; // Ensure it's always an array
                            }
                            

                            $data = $translations[$locale][$topic];
                            // dd($data);
                            $otherTranslations = [
                                'email_subject' => $data['email_subject'] ?? null,
                                'mail_body' => $data['mail_body'] ?? null,
                                'button_name' => $data['button_name'] ?? null,
                                'footer_content' => $data['footer_content'] ?? null,
                                'footer_copyrights' => $data['footer_copyrights'] ?? null,
                                'push_title' => $data['push_title'] ?? null,
                                'push_body' => $data['push_body'] ?? null,
                                'locale' => $locale,
                                'notification_channel_id' => $notificationChannel->id,
                            ];

                            // dd($otherTranslations);
                        // }

                        $exists = $notificationChannel->notificationChannelTranslationWords()
                                ->where('notification_channel_id',$notificationChannel->id)
                                ->where('locale', $locale)
                                ->first();
                        if (!$exists) {
                            $notificationChannel->notificationChannelTranslationWords()->insert($otherTranslations);
                        }       
                        $allTranslations[$locale] = json_encode($otherTranslations);
                    }
                    


                    // Store the translation dataset in JSON format for the notification channel
                    $notificationChannel->translation_dataset = json_encode($allTranslations);
                
                    // Save the updated notification channel with its translations
                    $notificationChannel->save();
                
                }
            }        
    }
}


