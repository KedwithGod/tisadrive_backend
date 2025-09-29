<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\LandingHeader;
use App\Models\Admin\LandingHome;
use App\Models\Admin\LandingDriver;
use App\Models\Admin\LandingAbouts;
use App\Models\Admin\LandingUser;
use App\Models\Admin\LandingContact;
use DB;
use Illuminate\Support\Str;
use App\Models\Admin\LandingQuickLink;

class LandingSiteSeeder extends Seeder
{
     
    public function run()
    { 

        $header = LandingHeader::first();

        if($header){
            goto home;

        }
        \DB::table('landing_headers')->insert([
            [
                'id' => Str::uuid(),
                'header_logo' => 'rest.png',
                'home' => 'Home',
                'aboutus' => 'About Us',
                'driver' => 'Driver',
                'user' => 'User',
                'contact' => 'Contact',
                'book_now_btn' => 'Book Now',
                'footer_logo' => 'rest.png',
                'footer_para' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, ',
                'quick_links' => 'Quick Links',
                'compliance' => 'Compliance',
                'privacy' => 'Privacy Policy',
                'terms' => 'Terms & Conditions',
                'dmv' => 'DMV Check',
                'user_app' => 'User Apps',
                'user_play' => 'Play Store',
                'user_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'user_apple' => 'Apple Store',
                'user_apple_link' => 'https://apps.apple.com/in/app/restart-user/id6738924393',
                'driver_app' => 'Driver Apps',
                'driver_play' => 'Play Store',
                'driver_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'driver_apple' => 'Apple Store',
                'driver_apple_link' => 'https://apps.apple.com/in/app/restart-driver/id6738922638',
                'copy_rights' => '2021 @ misoftwares',
                'fb_link' => 'https://www.facebook.com/',
                'linkdin_link' => 'https://in.linkedin.com/',
                'x_link' => 'https://x.com/',
                'insta_link' => 'https://www.instagram.com/',
                'locale' => 'En',
                'language' => 'English',
                'direction' => 'ltr',
            ],
             // Arabic Entry
             [
                'id' => Str::uuid(),
                'header_logo' => 'rest.png',
                'home' => 'الصفحة الرئيسية',
                'aboutus' => 'معلومات عنا',
                'driver' => 'السائق',
                'user' => 'المستخدم',
                'contact' => 'اتصل بنا',
                'book_now_btn' => 'احجز الآن',
                'footer_logo' => 'rest.png',
                'footer_para' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s, ',
                'quick_links' => 'روابط سريعة',
                'compliance' => 'الامتثال',
                'privacy' => 'سياسة الخصوصية',
                'terms' => 'الشروط والأحكام',
                'dmv' => 'فحص DMV',
                'user_app' => 'تطبيقات المستخدم',
                'user_play' => 'متجر Play',
                'user_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'user_apple' => 'متجر Apple',
                'user_apple_link' => 'https://apps.apple.com/in/app/restart-user-ar/id6738924393',
                'driver_app' => 'تطبيقات السائق',
                'driver_play' => 'متجر Play',
                'driver_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'driver_apple' => 'متجر Apple',
                'driver_apple_link' => 'https://apps.apple.com/in/app/restart-driver-ar/id6738922638',
                'copy_rights' => '2021 @ misoftwares',
                'fb_link' => 'https://www.facebook.com/',
                'linkdin_link' => 'https://in.linkedin.com/',
                'x_link' => 'https://x.com/',
                'insta_link' => 'https://www.instagram.com/',
                'locale' => 'Ar',
                'language' => 'Arabic',
                'direction' => 'rtl',
            ],
            // French Entry
            [
                'id' => Str::uuid(),
                'header_logo' => 'rest.png',
                'home' => 'Accueil',
                'aboutus' => 'À Propos',
                'driver' => 'Chauffeur',
                'user' => 'Utilisateur',
                'contact' => 'Contact',
                'book_now_btn' => 'Réservez Maintenant',
                'footer_logo' => 'rest.png',
                'footer_para' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,',
                'quick_links' => 'Liens Rapides',
                'compliance' => 'Conformité',
                'privacy' => 'Politique de Confidentialité',
                'terms' => 'Conditions Générales',
                'dmv' => 'Vérification DMV',
                'user_app' => 'Applications Utilisateurs',
                'user_play' => 'Play Store',
                'user_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'user_apple' => 'App Store',
                'user_apple_link' => 'https://apps.apple.com/in/app/restart-user-fr/id6738924393',
                'driver_app' => 'Applications Chauffeur',
                'driver_play' => 'Play Store',
                'driver_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'driver_apple' => 'App Store',
                'driver_apple_link' => 'https://apps.apple.com/in/app/restart-driver-fr/id6738922638',
                'copy_rights' => '2021 @ misoftwares',
                'fb_link' => 'https://www.facebook.com/',
                'linkdin_link' => 'https://in.linkedin.com/',
                'x_link' => 'https://x.com/',
                'insta_link' => 'https://www.instagram.com/',
                'locale' => 'Fr',
                'language' => 'French',
                'direction' => 'ltr',
            ],
            // spanish
            [
                'id' => Str::uuid(),
                'header_logo' => 'rest.png',
                'home' => 'Inicio',
                'aboutus' => 'Sobre Nosotros',
                'driver' => 'Conductor',
                'user' => 'Usuario',
                'contact' => 'Contacto',
                'book_now_btn' => 'Reservar Ahora',
                'footer_logo' => 'rest.png',
                'footer_para' => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industrys standard dummy text ever since the 1500s,',
                'quick_links' => 'Enlaces Rápidos',
                'compliance' => 'Cumplimiento',
                'privacy' => 'Política de Privacidad',
                'terms' => 'Términos y Condiciones',
                'dmv' => 'Verificación DMV',
                'user_app' => 'Aplicaciones de Usuario',
                'user_play' => 'Google Play',
                'user_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'user_apple' => 'App Store',
                'user_apple_link' => 'https://apps.apple.com/in/app/restart-user-alt/id6738924393',
                'driver_app' => 'Aplicaciones de Conductor',
                'driver_play' => 'Google Play',
                'driver_play_link' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'driver_apple' => 'App Store',
                'driver_apple_link' => 'https://apps.apple.com/in/app/restart-driver-alt/id6738922638',
                'copy_rights' => '2021 @ misoftwares - Alternativo',
                'fb_link' => 'https://www.facebook.com/',
                'linkdin_link' => 'https://in.linkedin.com/',
                'x_link' => 'https://x.com/',
                'insta_link' => 'https://www.instagram.com/',
                'locale' => 'Es',
                'language' => 'Spanish',
                'direction' => 'ltr',
            ],
    ]);

        home:
        $home = LandingHome::first();

        if($home){
            goto driver;

        }
        
        \DB::table('landing_homes')->insert([
            // English
            [
                'id' => Str::uuid(),
                'hero_title' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout!',
                'hero_user_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'hero_user_link_apple' => 'https://apps.apple.com/in/app/restart-user/id6738924393',
                'hero_driver_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'hero_driver_link_apple' => 'https://apps.apple.com/in/app/restart-driver/id6738922638',
                'feature_heading' => 'Advantage of using our Apps',
                'feature_para' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable.',
                'feature_sub_heading_1' => 'Tap a button, get a ride',
                'feature_sub_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_2' => 'Always on, always available',
                'feature_sub_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.',
                'feature_sub_heading_3' => 'Ride and Pay',
                'feature_sub_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_4' => 'You rate, we listen',
                'feature_sub_para_4' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'box_img_1' => '1.png',
                'box_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'box_img_2' => '2.jpg',
                'box_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical ',
                'box_img_3' => '3.jpg',
                'box_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text',
                'about_title_1' => 'ABOUT',
                'about_title_2' => 'The Company',
                'about_img' => 'company.png',
                'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
                'about_lists' => 'Dedicated Team Members,Awesome Services,Customer Support,Quality Assurance',
                'service_heading_1' => 'DIGITAL SERVICES',
                'service_heading_2' => 'A complete solution for your Taxi Service.',
                'service_para' => 'reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.',
                'services' => 'Data Protection,Customer Support,Quality Assurance,Awesome Services',
                'service_img' => 'service.png',
                'drive_heading' => 'Why Drive with Restart!',
                'drive_title_1' => 'About Us',
                'drive_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
                'drive_title_2' => 'Our Mission',
                'drive_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney',
                'drive_title_3' => 'Driver Commitment',
                'drive_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock',
                'service_area_img' => 'locations.png',
                'service_area_title' => 'Service Locations',
                'service_area_para' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.',
                'locale' => 'en',
                'language' => 'English',
                'direction' => 'ltr',
            ],
            // Arabic (locale: ar, direction: rtl)
            [
                'id' => Str::uuid(),
                'hero_title' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
                'hero_user_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'hero_user_link_apple' => 'https://apps.apple.com/in/app/restart-user/id6738924393',
                'hero_driver_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'hero_driver_link_apple' => 'https://apps.apple.com/in/app/restart-driver/id6738922638',
                'feature_heading' => 'مزايا استخدام تطبيقاتنا',
                'feature_para' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable.',
                'feature_sub_heading_1' => 'اضغط زرًا، واحصل على رحلة',
                'feature_sub_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_2' => 'متوفر دائمًا',
                'feature_sub_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.',
                'feature_sub_heading_3' => 'اركب وادفع',
                'feature_sub_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_4' => 'قيم رحلتك، نحن نستمع',
                'feature_sub_para_4' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'box_img_1' => '1.png',
                'box_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'box_img_2' => '2.jpg',
                'box_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical ',
                'box_img_3' => '3.jpg',
                'box_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text',
                'about_title_1' => 'حول',
                'about_title_2' => 'الشركة',
                'about_img' => 'company.png',
                'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
                'about_lists' => 'فريق متميز,خدمات رائعة,دعم العملاء,ضمان الجودة',
                'service_heading_1' => 'الخدمات الرقمية',
                'service_heading_2' => 'حل شامل لخدمة التاكسي الخاصة بك.',
                'service_para' => 'reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.',
                'services' => 'حماية البيانات,دعم العملاء,ضمان الجودة,خدمات رائعة',
                'service_img' => 'service.png',
                'drive_heading' => 'لماذا القيادة مع Restart!',
                'drive_title_1' => 'من نحن',
                'drive_para_1' => 'منصة تشارك الرحلات تربط الركاب بالسائقين بسهولة بضغطة زر.',
                'drive_title_2' => 'مهمتنا',
                'drive_para_2' => 'نهدف إلى توفير بيئة عمل مرنة وشاملة تعكس تنوع المدن التي نخدمها.',
                'drive_title_3' => 'التزامنا',
                'drive_para_3' => 'نعد بتوفير التكنولوجيا والدعم الذي يمكّنك من أن تكون مدير نفسك.',
                'service_area_img' => 'locations.png',
                'service_area_title' => 'مواقع الخدمة',
                'service_area_para' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.',
                'locale' => 'ar',
                'language' => 'Arabic',
                'direction' => 'rtl',
            ],
            // French (locale: fr, direction: ltr)
            [
                'id' => Str::uuid(),
                'hero_title' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout!',
                'hero_user_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'hero_user_link_apple' => 'https://apps.apple.com/in/app/restart-user/id6738924393',
                'hero_driver_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'hero_driver_link_apple' => 'https://apps.apple.com/in/app/restart-driver/id6738922638',
                'feature_heading' => 'Avantages de nos applications',
                'feature_para' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable.',
                'feature_sub_heading_1' => 'Appuyez sur un bouton, obtenez un trajet',
                'feature_sub_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_2' => 'Toujours disponible',
                'feature_sub_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_3' => 'Roulez et payez',
                'feature_sub_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_4' => 'Évaluez, nous écoutons',
                'feature_sub_para_4' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'box_img_1' => '1.png',
                'box_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'box_img_2' => '2.jpg',
                'box_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical ',
                'box_img_3' => '3.jpg',
                'box_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text',
                'about_title_1' => 'À propos',
                'about_title_2' => 'L\'entreprise',
                'about_img' => 'company.png',
                'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
                'about_lists' => 'Équipe dédiée,Services impressionnants,Soutien client,Assurance qualité',
                'service_heading_1' => 'Services numériques',
                'service_heading_2' => 'Une solution complète pour votre service de taxi.',
                'service_para' => 'reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.',
                'services' => 'Protection des données,Soutien client,Assurance qualité,Services impressionnants',
                'service_img' => 'service.png',
                'drive_heading' => 'Pourquoi conduire avec Restart!',
                'drive_title_1' => 'À propos de nous',
                'drive_para_1' => 'Une plateforme de covoiturage facilitant le transport avec un bouton.',
                'drive_title_2' => 'Notre mission',
                'drive_para_2' => 'Créer un environnement de travail inclusif et flexible.',
                'drive_title_3' => 'Engagement conducteur',
                'drive_para_3' => 'Fournir les technologies et le soutien nécessaires pour réussir.',
                'service_area_img' => 'locations.png',
                'service_area_title' => 'Zones de service',
                'service_area_para' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.',
                'locale' => 'fr',
                'language' => 'French',
                'direction' => 'ltr',
            ],
            // Spanish (locale: es, direction: ltr)
            [
                'id' => Str::uuid(),
                'hero_title' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout',
                'hero_user_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.user',
                'hero_user_link_apple' => 'https://apps.apple.com/in/app/restart-user/id6738924393',
                'hero_driver_link_android' => 'https://play.google.com/store/apps/details?id=com.restart.driver',
                'hero_driver_link_apple' => 'https://apps.apple.com/in/app/restart-driver/id6738922638',
                'feature_heading' => 'Beneficios de nuestras aplicaciones',
                'feature_para' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which dont look even slightly believable.',
                'feature_sub_heading_1' => 'Presiona un botón, consigue un viaje',
                'feature_sub_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_2' => 'Siempre disponible',
                'feature_sub_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_3' => 'Viaja y paga',
                'feature_sub_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'feature_sub_heading_4' => 'Evalúa, escuchamos',
                'feature_sub_para_4' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old',
                'box_img_1' => '1.png',
                'box_para_1' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'box_img_2' => '2.jpg',
                'box_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical ',
                'box_img_3' => '3.jpg',
                'box_para_3' => 'Contrary to popular belief, Lorem Ipsum is not simply random text',
                'about_title_1' => 'Sobre',
                'about_title_2' => 'la empresa',
                'about_img' => 'company.png',
                'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
                'about_lists' => 'Equipo dedicado,Servicios impresionantes,Atención al cliente,Aseguramiento de calidad',
                'service_heading_1' => 'Servicios digitales',
                'service_heading_2' => 'Una solución integral para tu servicio de taxi.',
                'service_para' => 'reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.',
                'services' => 'Protección de datos,Atención al cliente,Aseguramiento de calidad,Servicios impresionantes',
                'service_img' => 'service.png',
                'drive_heading' => '¡Por qué conducir con Restart!',
                'drive_title_1' => 'Quiénes somos',
                'drive_para_1' => 'Una plataforma de viajes compartidos que conecta pasajeros con conductores con solo un botón.',
                'drive_title_2' => 'Nuestra misión',
                'drive_para_2' => 'Crear un entorno laboral inclusivo y flexible que refleje la diversidad de las ciudades que servimos.',
                'drive_title_3' => 'Nuestro compromiso',
                'drive_para_3' => 'Proveer la tecnología y el apoyo necesarios para que seas tu propio jefe.',
                'service_area_img' => 'locations.png',
                'service_area_title' => 'Áreas de servicio',
                'service_area_para' => 'The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested.',
                'locale' => 'es',
                'language' => 'Spanish',
                'direction' => 'ltr',
            ],
    ]);

    driver:
   
    $driver = LandingDriver::first();

        if($driver){
            goto user;

        }
        
        \DB::table('landing_drivers')->insert([
            [
                'id' => Str::uuid(),
                'hero_title' => 'Driver',
                'driver_heading_1' => 'Be your own boss',
                'driver_para' => 'HOURS ARE Foto de perfil del conductor, Registro del vehículo, Inspección del vehículo, Seguro del vehículo OR WEEKENDS.',
                'driver_img_1' => 'app-download.png',
                'driver_title_1' => 'Download',
                'driver_para_1' => 'Download Foto de perfil del conductor, Registro del vehículo, Inspección del vehículo, Seguro del vehículo the Google Play or App Store on your smartphone.',
                'driver_img_2' => 'upload.png',
                'driver_title_2' => 'Upload',
                'driver_para_2' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots .',
                'driver_img_3' => 'drive.png',
                'driver_title_3' => 'Drive',
                'driver_para_3' => 'Drive and earn as much Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots a trip plus tips.',
                'how_it_work_heading' => 'How It Works', 
                'how_it_work_title_1' => 'Get the App', 
                'how_it_work_para_1' => 'Get theContrary to popular belief, Lorem Ipsum is not simply random text. It has roots e Play',
                'how_it_work_img_1' => 'd1.png',
                'how_it_work_title_2' => 'Apply to drive', 
                'how_it_work_para_2' => 'You can Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  App',
                'how_it_work_img_2' => 'd2.png',
                'how_it_work_title_3' => 'Get Approved', 
                'how_it_work_para_3' => 'After Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots you’re ready to hit the road and start earning.',
                'how_it_work_img_3' => 'd3.png',
                'how_it_work_title_4' => 'Open App', 
                'how_it_work_para_4' => 'Open Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots r mode',
                'how_it_work_img_4' => 'd4.png',
                'how_it_work_title_5' => 'Accept', 
                'how_it_work_para_5' => 'Accept Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots request.',
                'how_it_work_img_5' => 'd5.png',
                'how_it_work_title_6' => 'Pickup', 
                'how_it_work_para_6' => 'Pick up Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots location',
                'how_it_work_img_6' => 'd6.png',
                'how_it_work_title_7' => 'Drop off', 
                'how_it_work_para_7' => 'Drop off Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots destination.',
                'how_it_work_img_7' => 'd7.png',
                'req_heading' => 'Requirements for Drive',
                'req_title' => 'What you will need to apply with us',
                'req_lists' => 'You must have a Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots .,
                                You mustContrary to popular belief, Lorem Ipsum is not simply random text. It has roots .,You consent to our driver screening and background check.,
                                You must own an iPhone Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  and run the app.',
                'req_img' => 'drive-apply.png',
                'vechile_req_title' => 'Vehicle Requirements',
                'vechile_req_lists' => '2008 or neweContrary to popular belief, Lorem Ipsum is not simply random text. It has roots *Car year may vary by region',
                'vechile_req_img' => 'taxi-req.png',
                'doc_req_title' => 'Document requirements',
                'doc_req_lists' => 'Driver profile Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  inspection',
                'doc_req_img' => 'document.png',
                'locale' => 'en',
                'language' => 'English',
                'direction' => 'ltr',
            ],
            // Arabic
            [
                'id' => Str::uuid(),
                'hero_title' => 'سائق',
                'driver_heading_1' => 'كن رئيس نفسك',
                'driver_para' => 'الساعات مرنة Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  نهاية الأسبوع.',
                'driver_img_1' => 'app-download.png',
                'driver_title_1' => 'تحميل',
                'driver_para_1' => 'قم بتحميل تطبيContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  الذكي.',
                'driver_img_2' => 'upload.png',
                'driver_title_2' => 'تحميل المستندات',
                'driver_para_2' => 'قم بتحميل Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  على الموافقة.',
                'driver_img_3' => 'drive.png',
                'driver_title_3' => 'القيادة',
                'driver_para_3' => 'قُد واربح بقدContrary to popular belief, Lorem Ipsum is not simply random text. It has roots الإكراميات.',
                'how_it_work_heading' => 'كيف تعمل',
                'how_it_work_title_1' => 'احصل على التطبيق',
                'how_it_work_para_1' => 'احصل علىContrary to popular belief, Lorem Ipsum is not simply random text. It has roots Google Play',
                'how_it_work_img_1' => 'd1.png',
                'how_it_work_title_2' => 'التقديم للقيادة',
                'how_it_work_para_2' => 'يمكنك Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  السائق.',
                'how_it_work_img_2' => 'd2.png',
                'how_it_work_title_3' => 'الحصول على الموافقة',
                'how_it_work_para_3' => 'بعد تحميل المستنداتContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  في الكسب.',
                'how_it_work_img_3' => 'd3.png',
                'how_it_work_title_4' => 'افتح التطبيق',
                'how_it_work_para_4' => 'افتح Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  السائق.',
                'how_it_work_img_4' => 'd4.png',
                'how_it_work_title_5' => 'قبول',
                'how_it_work_para_5' => 'قبولContrary to popular belief, Lorem Ipsum is not simply random text. It has roots راكب.',
                'how_it_work_img_5' => 'd5.png',
                'how_it_work_title_6' => 'استلام',
                'how_it_work_para_6' => 'قم Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  موقعه.',
                'how_it_work_img_6' => 'd6.png',
                'how_it_work_title_7' => 'التوصيل',
                'how_it_work_para_7' => 'قم Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  وجهته.',
                'how_it_work_img_7' => 'd7.png',
                'req_heading' => 'متطلبات القيادة',
                'req_title' => 'ما ستحتاجه للتقديم معنا',
                'req_lists' => 'يجب أن Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  ولايات أخرى.,
                                يجContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  أكثر.,
                                يجب أن يكوContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  السيارة.,
                                موافقتك على فحص الخلفية.,
                                يجب أن تمتلك هاتفًا ذكيًا (iPhone أو Android) يمكنه تحميل وتشغيل التطبيق.',
                'req_img' => 'drive-apply.png',
                'vechile_req_title' => 'متطلبات المركبة',
                'vechile_req_lists' => '2008 Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots نيع حسب المنطقة',
                'vechile_req_img' => 'taxi-req.png',
                'doc_req_title' => 'متطلبات المستندات',
                'doc_req_lists' => 'صورة الملف الشContrary to popular belief, Lorem Ipsum is not simply random text. It has roots السيارة',
                'doc_req_img' => 'document.png',
                'locale' => 'ar',
                'language' => 'Arabic',
                'direction' => 'rtl',
            ],
             // French
            [
                'id' => Str::uuid(),
                'hero_title' => 'Conducteur',
                'driver_heading_1' => 'Soyez votre propre patron',
                'driver_para' => 'LES HEURES SONT Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  EN SEMAINE OU LE WEEK-END.',
                'driver_img_1' => 'app-download.png',
                'driver_title_1' => 'Télécharger',
                'driver_para_1' => 'TéléchargezContrary to popular belief, Lorem Ipsum is not simply random text. It has roots otre smartphone.',
                'driver_img_2' => 'upload.png',
                'driver_title_2' => 'Télécharger',
                'driver_para_2' => 'Téléchargez vos Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  une approbation.',
                'driver_img_3' => 'drive.png',
                'driver_title_3' => 'Conduire',
                'driver_para_3' => 'Conduisez et Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots pour le temps et la distance d\'un trajet plus les pourboires.',
                'how_it_work_heading' => 'Comment ça fonctionne',
                'how_it_work_title_1' => 'Obtenez l\'application',
                'how_it_work_para_1' => 'Obtenez Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  Play',
                'how_it_work_img_1' => 'd1.png',
                'how_it_work_title_2' => 'Postulez pour conduire',
                'how_it_work_para_2' => 'Vous pouvez remplir Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots conducteur',
                'how_it_work_img_2' => 'd2.png',
                'how_it_work_title_3' => 'Obtenez une approbation',
                'how_it_work_para_3' => 'Après Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots vous êtes prêt à prendre la route et à commencer à gagner.',
                'how_it_work_img_3' => 'd3.png',
                'how_it_work_title_4' => 'Ouvrez l\'application',
                'how_it_work_para_4' => 'Ouvrez l\'application et activez le mode conducteur',
                'how_it_work_img_4' => 'd4.png',
                'how_it_work_title_5' => 'Acceptez',
                'how_it_work_para_5' => 'Acceptez Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots passager.',
                'how_it_work_img_5' => 'd5.png',
                'how_it_work_title_6' => 'Récupérez',
                'how_it_work_para_6' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'how_it_work_img_6' => 'd6.png',
                'how_it_work_title_7' => 'Déposez',
                'how_it_work_para_7' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots ',
                'how_it_work_img_7' => 'd7.png',
                'req_heading' => 'Exigences pour conduire',
                'req_title' => 'Ce dont vous aurez besoin pour postuler chez nous',
                'req_lists' => 'Vous devez avoir Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots oraires ou d\'autres États sont également acceptables.,
                                Vous Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots s pour conduire.,
                                Vous devez avoir un dossier de conduite propre avec une assurance automobile.,
                                CContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  It has roots ,
                                Vous devez posséder un smartphone iPhone ou Android pouvant télécharger et exécuter l\'application.',
                'req_img' => 'drive-apply.png',
                'vechile_req_title' => 'Exigences du véhicule',
                'vechile_req_lists' => '2008 ou plus Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  y compris le siège du conducteur,Véhicule enregistré,*L\'année du véhicule peut varier selon la région',
                'vechile_req_img' => 'taxi-req.png',
                'doc_req_title' => 'Exigences en matière de documents',
                'doc_req_lists' => 'Photo deContrary to popular belief, Lorem Ipsum is not simply random text. It has roots du véhicule',
                'doc_req_img' => 'document.png',
                'locale' => 'fr',
                'language' => 'French',
                'direction' => 'ltr',
            ],
             // Spanish
             [
                'id' => Str::uuid(),
                'hero_title' => 'Conductor',
                'driver_heading_1' => 'Sé tu propio jefe',
                'driver_para' => 'LAS HORAS SON Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  SEMANA O LOS FINES DE SEMANA.',
                'driver_img_1' => 'app-download.png',
                'driver_title_1' => 'Descargar',
                'driver_para_1' => 'Descarga la Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  inteligente.',
                'driver_img_2' => 'upload.png',
                'driver_title_2' => 'Subir',
                'driver_para_2' => 'Sube tusContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  la aprobación.',
                'driver_img_3' => 'drive.png',
                'driver_title_3' => 'Conducir',
                'driver_para_3' => 'Conduce y Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots del viaje, más propinas.',
                'how_it_work_heading' => 'Cómo funciona',
                'how_it_work_title_1' => 'Obtén la app',
                'how_it_work_para_1' => 'Obtén la Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  Play',
                'how_it_work_img_1' => 'd1.png',
                'how_it_work_title_2' => 'Solicitar para conducir',
                'how_it_work_para_2' => 'Puedes Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  conductores.',
                'how_it_work_img_2' => 'd2.png',
                'how_it_work_title_3' => 'Obtener la aprobación',
                'how_it_work_para_3' => 'Después de Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots estarás listo para salir y empezar a ganar.',
                'how_it_work_img_3' => 'd3.png',
                'how_it_work_title_4' => 'Abrir la app',
                'how_it_work_para_4' => 'Abre laContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  conductor.',
                'how_it_work_img_4' => 'd4.png',
                'how_it_work_title_5' => 'Aceptar',
                'how_it_work_para_5' => 'Acepta Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots  pasajero.',
                'how_it_work_img_5' => 'd5.png',
                'how_it_work_title_6' => 'Recoger',
                'how_it_work_para_6' => 'RecogeContrary to popular belief, Lorem Ipsum is not simply random text. It has roots  ubicación.',
                'how_it_work_img_6' => 'd6.png',
                'how_it_work_title_7' => 'Dejar',
                'how_it_work_para_7' => 'Deja ato popular belief, Lorem Ipsum is not simply random text. It has rootsino.',
                'how_it_work_img_7' => 'd7.png',
                'req_heading' => 'Requisitos para conducir',
                'req_title' => 'Lo que necesitarás para aplicar con nosotros',
                'req_lists' => 'Debes tener una to popular belief, Lorem Ipsum is not simply random text. It has rootso de otro estado., 
                                Debes tto popular belief, Lorem Ipsum is not simply random text. It has roots., 
                                Debes tener un to popular belief, Lorem Ipsum is not simply random text. It has roots automóvil., 
                                Aceptas nuestra revisión de antecedentes., 
                                Debes to popular belief, Lorem Ipsum is not simply random text. It has rootsejecutar la aplicación.',
                'req_img' => 'drive-apply.png',
                'vechile_req_title' => 'Requisitos del vehículo',
                'vechile_req_lists' => '2008 o más to popular belief, Lorem Ipsum is not simply random text. It has roots, vehículo registrado, *El año del vehículo puede variar según la región.',
                'vechile_req_img' => 'taxi-req.png',
                'doc_req_title' => 'Requisitos de documentos',
                'doc_req_lists' => 'Foto de perfil delto popular belief, Lorem Ipsum is not simply random text. It has roots vehículo',
                'doc_req_img' => 'document.png',
                'locale' => 'es',
                'language' => 'Spanish',
                'direction' => 'ltr',
            ],
    ]);

    user:

    $user = LandingUser::first();

        if($user){
            goto contact;

        }

        // \Log::info('Seeding LandingUser...');
        \DB::table('landing_users')->insert([
         [
            'id' => Str::uuid(),
            'hero_title' => 'User',
            'user_heading_1' => 'Be your own ride',
            'user_para' => 'HOURS ARE It is a long established fact that a reader will be distracted by the readable WEEKDAYS, OR WEEKENDS.',
            'user_img_1' => 'app-download.png',
            'user_title_1' => 'Download',
            'user_para_1' => 'Download It is a long established fact that a reader will be distracted by the readablesmartphone.',
            'user_img_2' => '2.jpg',
            'user_title_2' => 'Sign In',
            'user_para_2' => 'SIt is a long established fact that a reader will be distracted by the readable.',
            'user_img_3' => 'drive.png',
            'user_title_3' => 'Ride',
            'user_para_3' => 'It is a long established fact that a reader will be distracted by the readable!',
            'how_it_work_heading' => 'How It Works', 
            'how_it_work_title_1' => 'Get the App', 
            'how_it_work_para_1' => 'It is a long established fact that a reader will be distracted by the readable',
            'how_it_work_img_1' => 'u1.png',
            'how_it_work_title_2' => 'Signup to Ride', 
            'how_it_work_para_2' => 'It is a long established fact that a reader will be distracted by the readable',
            'how_it_work_img_2' => 'u2.png',
            'how_it_work_title_3' => 'Select Location', 
            'how_it_work_para_3' => 'It is a long established fact that a reader will be distracted by the readable',
            'how_it_work_img_3' => 'u3.png',
            'how_it_work_title_4' => 'Select Vehicle', 
            'how_it_work_para_4' => 'It is a long established fact that a reader will be distracted by the readable',
            'how_it_work_img_4' => 'u4.png',
            'how_it_work_title_5' => 'Ride', 
            'how_it_work_para_5' => 'It is a long established fact that a reader will be distracted by the readable!',
            'how_it_work_img_5' => 'u5.png',
            'how_it_work_title_6' => 'Pay & Rating', 
            'how_it_work_para_6' => 'It is a long established fact that a reader will be distracted by the readable!',
            'how_it_work_img_6' => 'u6.png',
            'locale' => 'En',
            'language' => 'English',
            'direction' => 'ltr',
            
         ],
        //  arabic
        [
            'id' => Str::uuid(),
        'hero_title' => 'المستخدم',
        'user_heading_1' => 'كن رحلتك الخاصة',
        'user_para' => 'الساعات مرنة تمامًا. It is a long established fact that a reader will be distracted by the readableعطلات نهاية الأسبوع.',
        'user_img_1' => 'app-download.png',
        'user_title_1' => 'تحميل',
        'user_para_1' => 'قم بتنزيل تطبيق It is a long established fact that a reader will be distracted by the readable',
        'user_img_2' => '2.jpg',
        'user_title_2' => 'تسجيل الدخول',
        'user_para_2' => 'سجل باستخدام It is a long established fact that a reader will be distracted by the readable',
        'user_img_3' => 'drive.png',
        'user_title_3' => 'الركوب',
        'user_para_3' => 'اركب إلى حيث تريد أن تذهب!',
        'how_it_work_heading' => 'كيف يعمل',
        'how_it_work_title_1' => 'الحصول على التطبيق',
        'how_it_work_para_1' => 'احصل علIt is a long established fact that a reader will be distracted by the readablele Play',
        'how_it_work_img_1' => 'u1.png',
        'how_it_work_title_2' => 'التسجيل للركوب',
        'how_it_work_para_2' => 'يIt is a long established fact that a reader will be distracted by the readableفي تطبيق Restart User',
        'how_it_work_img_2' => 'u2.png',
        'how_it_work_title_3' => 'حدد الموقع',
        'how_it_work_para_3' => 'بعد التسجيل، It is a long established fact that a reader will be distracted by the readable لرحلتك.',
        'how_it_work_img_3' => 'u3.png',
        'how_it_work_title_4' => 'اختيار المركبة',
        'how_it_work_para_4' => 'اIt is a long established fact that a reader will be distracted by the readableتك.',
        'how_it_work_img_4' => 'u4.png',
        'how_it_work_title_5' => 'الركوب',
        'how_it_work_para_5' => 'رحIt is a long established fact that a reader will be distracted by the readableهتك!',
        'how_it_work_img_5' => 'u5.png',
        'how_it_work_title_6' => 'الدفع والتقييم',
        'how_it_work_para_6' => 'بعدIt is a long established fact that a reader will be distracted by the readableة رحلتك!',
        'how_it_work_img_6' => 'u6.png',
        'locale' => 'ar',
        'language' => 'Arabic',
        'direction' => 'rtl',
            
         ],
        //  french
        [
            'id' => Str::uuid(),
        'hero_title' => 'Utilisateur',
        'user_heading_1' => 'Soyez votre propre trajet',
        'user_para' => 'LES HEURES SONT It is a long established fact that a reader will be distracted by the readableLE WEEK-END.',
        'user_img_1' => 'app-download.png',
        'user_title_1' => 'Télécharger',
        'user_para_1' => 'Téléchargez It is a long established fact that a reader will be distracted by the readablesmartphone.',
        'user_img_2' => '2.jpg',
        'user_title_2' => 'Se connecter',
        'user_para_2' => 'InscrivezIt is a long established fact that a reader will be distracted by the readable mobile.',
        'user_img_3' => 'drive.png',
        'user_title_3' => 'Conduire',
        'user_para_3' => 'RoulezIt is a long established fact that a reader will be distracted by the readablealler!',
        'how_it_work_heading' => 'Comment ça marche',
        'how_it_work_title_1' => 'Obtenez l\'application',
        'how_it_work_para_1' => 'ObtenezIt is a long established fact that a reader will be distracted by the readable Google Play',
        'how_it_work_img_1' => 'u1.png',
        'how_it_work_title_2' => 'Inscrivez-vous pour conduire',
        'how_it_work_para_2' => 'Vous pouvez It is a long established fact that a reader will be distracted by the readable Restart User',
        'how_it_work_img_2' => 'u2.png',
        'how_it_work_title_3' => 'Sélectionnez un emplacement',
        'how_it_work_para_3' => 'Après l\'It is a long established fact that a reader will be distracted by the readablepour votre trajet.',
        'how_it_work_img_3' => 'u3.png',
        'how_it_work_title_4' => 'Sélectionnez un véhicule',
        'how_it_work_para_4' => 'It is a long established fact that a reader will be distracted by the readable.',
        'how_it_work_img_4' => 'u4.png',
        'how_it_work_title_5' => 'Rouler',
        'how_it_work_para_5' => 'Bon It is a long established fact that a reader will be distracted by the readable!',
        'how_it_work_img_5' => 'u5.png',
        'how_it_work_title_6' => 'Payer et noter',
        'how_it_work_para_6' => 'AprèsIt is a long established fact that a reader will be distracted by the readableconduite!',
        'how_it_work_img_6' => 'u6.png',
        'locale' => 'fr',
        'language' => 'French',
        'direction' => 'ltr',
            
         ],
        //  spanish
        [
           'id' => Str::uuid(),
        'hero_title' => 'Usuario',
        'user_heading_1' => 'Sé tu propio viaje',
        'user_para' => 'LAS HORAS SON TOTALMENTE It is a long established fact that a reader will be distracted by the readable DURANTE LA SEMANA O LOS FINES DE SEMANA.',
        'user_img_1' => 'app-download.png',
        'user_title_1' => 'Descargar',
        'user_para_1' => 'Descarga la It is a long established fact that a reader will be distracted by the readable inteligente.',
        'user_img_2' => '2.jpg',
        'user_title_2' => 'Iniciar sesión',
        'user_para_2' => 'Regístrate It is a long established fact that a reader will be distracted by the readable móvil.',
        'user_img_3' => 'drive.png',
        'user_title_3' => 'Conducir',
        'user_para_3' => '¡Conduce a donde quieras ir!',
        'how_it_work_heading' => 'Cómo funciona',
        'how_it_work_title_1' => 'Obtén la aplicación',
        'how_it_work_para_1' => 'Obtén la It is a long established fact that a reader will be distracted by the readable Play',
        'how_it_work_img_1' => 'u1.png',
        'how_it_work_title_2' => 'Regístrate para conducir',
        'how_it_work_para_2' => 'Puedes It is a long established fact that a reader will be distracted by the readable User',
        'how_it_work_img_2' => 'u2.png',
        'how_it_work_title_3' => 'Selecciona ubicación',
        'how_it_work_para_3' => 'Después del rIt is a long established fact that a reader will be distracted by the readable para tu viaje.',
        'how_it_work_img_3' => 'u3.png',
        'how_it_work_title_4' => 'Seleccionar vehículo',
        'how_it_work_para_4' => 'Elige tu vehículo.',
        'how_it_work_img_4' => 'u4.png',
        'how_it_work_title_5' => 'Viajar',
        'how_it_work_para_5' => '¡Feliz It is a It is a long established fact that a reader will be distracted by the readable readable!',
        'how_it_work_img_5' => 'u5.png',
        'how_it_work_title_6' => 'Pagar y calificar',
        'how_it_work_para_6' => 'Después de It is a long established fact that a reader will be distracted by the readable de viaje.',
        'how_it_work_img_6' => 'u6.png',
        'locale' => 'es',
        'language' => 'Spanish',
        'direction' => 'ltr',
            
         ],
    ]);

    contact:
    $contact = LandingContact::first();

        if($contact){
            goto quick;

        }
        
        // \Log::info('Seeding LandingContact...');
        \DB::table('landing_contacts')->insert([
           [
            'id' => Str::uuid(),
            'hero_title' => 'Contact',
            'contact_heading' => 'Get In Touch',
            'contact_para' => 'Have a question,It is a long established fact that a reader will be distracted by the readable.',
            'contact_address_title' => 'OFFICE ADDRESS',
            'contact_address' => 'Mobility Intelligence It is a long established fact that a reader will be distracted by the readable Coimbatore, Tamil Nadu 641035',
            'contact_phone_title' => 'MOBILE',
            'contact_phone' => '+91-9876543210',
            'contact_mail_title' => 'MAIL',
            'contact_mail' => 'dilip@misoftwares.com',
            'contact_web_title' => 'WEBSITE',
            'contact_web' => 'https://misoftwares.in/',
            'form_name' => 'Name',
            'form_mail' => 'Email',
            'form_subject' => 'Subject',
            'form_message' => 'Message',
            'form_btn' => 'Send Message',
            'locale' => 'En',
            'language' => 'English',
            'direction' => 'ltr',
            
           ],
        //    arabic
        [
            'id' => Str::uuid(),
            'hero_title' => 'اتصل',
            'contact_heading' => 'تواصل معنا',
            'contact_para' => 'هل لديك سؤال أو استفسار أو تعليق؟ لا It is a long established fact that a reader will be distracted by the readable',
            'contact_address_title' => 'عنوان المكتب',
            'contact_address' => 'البرمجيات الذكية للتنقل، الطابق الثاني، 37A11، طريIt is a long established fact that a reader will be distracted by the readable41035',
            'contact_phone_title' => 'الجوال',
            'contact_phone' => '+91-9876543210',
            'contact_mail_title' => 'البريد الإلكتروني',
            'contact_mail' => 'dilip@misoftwares.com',
            'contact_web_title' => 'الموقع الإلكتروني',
            'contact_web' => 'https://misoftwares.in/',
            'form_name' => 'الاسم',
            'form_mail' => 'البريد الإلكتروني',
            'form_subject' => 'الموضوع',
            'form_message' => 'الرسالة',
            'form_btn' => 'إرسال الرسالة',
            'locale' => 'ar',
            'language' => 'Arabic',
            'direction' => 'rtl',
        ],
        // french
        [
            'id' => Str::uuid(),
            'hero_title' => 'Contact',
            'contact_heading' => 'Contactez-nous',
            'contact_para' => 'Vous avezIt is a long established fact that a reader will be distracted by the readable envoyer un message. Nous ferons tout notre possible pour répondre rapidement.',
            'contact_address_title' => 'ADRESSE DU BUREAU',
            'contact_address' => 'Mobility It is a long established fact that a reader will be distracted by the readable Nadu 641035',
            'contact_phone_title' => 'TÉLÉPHONE',
            'contact_phone' => '+91-9876543210',
            'contact_mail_title' => 'MAIL',
            'contact_mail' => 'dilip@misoftwares.com',
            'contact_web_title' => 'SITE WEB',
            'contact_web' => 'https://misoftwares.in/',
            'form_name' => 'Nom',
            'form_mail' => 'Email',
            'form_subject' => 'Sujet',
            'form_message' => 'Message',
            'form_btn' => 'Envoyer le message',
            'locale' => 'fr',
            'language' => 'French',
            'direction' => 'ltr',
        ],
        // spanish
        [
            'id' => Str::uuid(),
            'hero_title' => 'Contacto',
            'contact_heading' => 'Póngase en contacto',
            'contact_para' => '¿Tienes una It is a long established fact that a reader will be distracted by the readableido y envíenos un mensaje. Haremos todo lo posible para responder rápidamente.',
            'contact_address_title' => 'DIRECCIÓN DE LA OFICINA',
            'contact_address' => 'Mobility IntelligenceIt is a long established fact that a reader will be distracted by the readableTamil Nadu 641035',
            'contact_phone_title' => 'MÓVIL',
            'contact_phone' => '+91-9876543210',
            'contact_mail_title' => 'CORREO ELECTRÓNICO',
            'contact_mail' => 'dilip@misoftwares.com',
            'contact_web_title' => 'SITIO WEB',
            'contact_web' => 'https://misoftwares.in/',
            'form_name' => 'Nombre',
            'form_mail' => 'Correo electrónico',
            'form_subject' => 'Asunto',
            'form_message' => 'Mensaje',
            'form_btn' => 'Enviar mensaje',
            'locale' => 'es',
            'language' => 'Spanish',
            'direction' => 'ltr',
        ]
    ]);

    quick:
    $quick = LandingQuickLink::first();

        if($quick){
            goto abouts;

        }
        
        // \Log::info('Seeding LandingQuickLink...');
        \DB::table('landing_quicklinks')->insert([
           [
            'id' => Str::uuid(),
            'privacy_title' => 'Privacy Policy',
            'privacy' =>'<h2>Privacy Policy</h2>

                <p>The Scope of This Policy</p>
                
                <p>This policy applies to all Mi Softwares users, including Riders and Drivers (including Driver applicants), and to all Mi Softwares platforms and services, including our apps, websites, features, and other services (collectively, the &ldquo;Mi Softwares Platform&rdquo;). Please remember that your use of the Mi Softwares Platform is also subject to our Terms of Service.</p>
                
                <p>The Information We Collect</p>
                
                <p>When you use the Mi Softwares Platform, we collect the information you provide, usage information, and information about your device. We also collect information about you from other sources like third-party services, and optional programs in which you participate, which we may combine with other information we have about you. Here are the types of information we collect about you:</p>
                
                <p>A. Information You Provide to Us</p>
                
                <p>Account Registration. When you create an account with Mi Softwares, we collect the information you provide us, such as your name, email address, phone number, and payment information. You may choose to share additional info with us for your Rider profile, like your photo or saved addresses (e.g., home or work), and set up other preferences (such as your preferred pronouns).<br />
                <br />
                <strong>Driver Information.</strong> If you apply to be a Driver, we will collect the information you provide in your application, including your name, email address, phone number, birth date, profile photo, physical address, government identification number (such as social security number), driver&rsquo;s license information, vehicle information, and car insurance information. We collect the payment information you provide us, including your bank routing numbers, and tax information. Depending on where you want to drive, we may also ask for additional business license or permit information or other information to manage driving and programs relevant to that location. We may need additional information from you at some point after you become a Driver, including information to confirm your identity (like a photo).<br />
                <br />
                <strong>SOS Contact Information</strong> We will collect the contact list permission to list the contacts while adding the sos contacts for user & driver app<br />
                <br />
                <strong>Ratings and Feedback.</strong> When you rate and provide feedback about Riders or Drivers, we collect all of the information you provide in your feedback.<br />
                <br />
                <strong>Communications.</strong> When you contact us or we contact you, we collect any information that you provide, including the contents of the messages or attachments you send us.</p>
                
                <p>B. Information We Collect When You Use the Mi Softwares Platform</p>
                
                <p><strong>Location Information.</strong> Great rides start with an easy and accurate pickup. The Mi Softwares Platform collects location information (including GPS and WiFi data) differently depending on your Mi Softwares app settings and device permissions as well as whether you are using the platform as a Rider or Driver:</p>
                
                <ul>
                <li>Riders: We collect your device&rsquo;s precise location when you open and use the Mi Softwares app, including while the app is running in the background from the time you request a ride until it ends. Mi Softwares also tracks the precise location of scooters and e-bikes at all times.</li>
                <li>Drivers: We collect your device&rsquo;s precise location when you open and use the app, including while the app is running in the background when it is in driver mode. We also collect precise location for a limited time after you exit driver mode in order to detect ride incidents, and continue collecting it until a reported or detected incident is no longer active.</li>
                </ul>
                
                <p><strong>Usage Information.</strong> We collect information about your use of the Mi Softwares Platform, including ride information like the date, time, destination, distance, route, payment, and whether you used a promotional or referral code. We also collect information about your interactions with the Mi Softwares Platform like our apps and websites, including the pages and content you view and the dates and times of your use.<br />
                <br />
                <strong>Device Information.</strong> We collect information about the devices you use to access the Mi Softwares Platform, including device model, IP address, type of browser, version of operating system, identity of carrier and manufacturer, radio type (such as 4G), preferences and settings (such as preferred language), application installations, device identifiers, advertising identifiers, and push notification tokens. If you are a Driver, we also collect mobile sensor data from your device (such as speed, direction, height, acceleration, deceleration, and other technical data).<br />
                <br />
                <strong>Communications Between Riders and Drivers.</strong> We work with a third party to facilitate phone calls and text messages between Riders and Drivers without sharing either party&rsquo;s actual phone number with the other. But while we use a third party to provide the communication service, we collect information about these communications, including the participants&rsquo; phone numbers, the date and time, and the contents of SMS messages. For security purposes, we may also monitor or record the contents of phone calls made through the Mi Softwares Platform, but we will always let you know we are about to do so before the call begins.<br />
                <br />
                <strong>Address Book Contacts.</strong> You may set your device permissions to grant Mi Softwares access to your contact lists and direct Mi Softwares to access your contact list, for example to help you refer friends to Mi Softwares. If you do this, we will access and store the names and contact information of the people in your address book.<br />
                <br />
                <strong>Cookies, Analytics, and Third-Party Technologies.</strong> We collect information through the use of &ldquo;cookies&rdquo;, tracking pixels, data analytics tools like Google Analytics, SDKs, and other third-party technologies to understand how you navigate through the Mi Softwares Platform and interact with Mi Softwares advertisements, to make your Mi Softwares experience safer, to learn what content is popular, to improve your site experience, to serve you better ads on other sites, and to save your preferences. Cookies are small text files that web servers place on your device; they are designed to store basic information and to help websites and apps recognize your browser. We may use both session cookies and persistent cookies. A session cookie disappears after you close your browser. A persistent cookie remains after you close your browser and may be accessed every time you use the Mi Softwares Platform. You should consult your web browser(s) to modify your cookie settings. Please note that if you delete or choose not to accept cookies from us, you may miss out on certain features of the Mi Softwares Platform.</p>
                
                <p>C. Information We Collect from Third Parties</p>
                
                <p><strong>Third-Party Services.</strong> Third-party services provide us with information needed for core aspects of the Mi Softwares Platform, as well as for additional services, programs, loyalty benefits, and promotions that can enhance your Mi Softwares experience. These third-party services include background check providers, insurance partners, financial service providers, marketing providers, and other businesses. We obtain the following information about you from these third-party services:</p>
                
                <ul>
                <li>Information to make the Mi Softwares Platform safer, like background check information for drivers;</li>
                <li>Information about your participation in third-party programs that provide things like insurance coverage and financial instruments, such as insurance, payment, transaction, and fraud detection information;</li>
                <li>Information to operationalize loyalty and promotional programs or applications, services, or features you choose to connect or link to your Mi Softwares account, such as information about your use of such programs, applications, services, or features; and</li>
                <li>Information about you provided by specific services, such as demographic and market segment information.</li>
                </ul>
                
                <p><strong>Enterprise Programs.</strong> If you use Mi Softwares through your employer or other organization that participates in one of our Mi Softwares Business enterprise programs, we will collect information about you from those parties, such as your name and contact information.<br />
                <br />
                <strong>Concierge Service.</strong> Sometimes another business or entity may order you a Mi Softwares ride. If an organization has ordered a ride for you using our Concierge service, they will provide us your contact information and the pickup and drop-off location of your ride.<br />
                <br />
                <strong>Referral Programs.</strong> Friends help friends use the Mi Softwares Platform. If someone refers you to Mi Softwares, we will collect information about you from that referral including your name and contact information.<br />
                <br />
                <strong>Other Users and Sources.</strong> Other users or public or third-party sources such as law enforcement, insurers, media, or pedestrians may provide us information about you, for example as part of an investigation into an incident or to provide you support.</p>
                
                <p>How We Use Your Information</p>
                
                <p>We use your personal information to:</p>
                
                <ul>
                <li>Provide the Mi Softwares Platform;</li>
                <li>Maintain the security and safety of the Mi Softwares Platform and its users;</li>
                <li>Build and maintain the Mi Softwares community;</li>
                <li>Provide customer support;</li>
                <li>Improve the Mi Softwares Platform; and</li>
                <li>Respond to legal proceedings and obligations.</li>
                </ul>
                
                <p><strong>Providing the Mi Softwares Platform.</strong> We use your personal information to provide an intuitive, useful, efficient, and worthwhile experience on our platform. To do this, we use your personal information to:</p>
                
                <ul>
                <li>Verify your identity and maintain your account, settings, and preferences;</li>
                <li>Connect you to your rides and track their progress;</li>
                <li>Calculate prices and process payments;</li>
                <li>Allow Riders and Drivers to connect regarding their ride and to choose to share their location with others;</li>
                <li>Communicate with you about your rides and experience;</li>
                <li>Collect feedback regarding your experience;</li>
                <li>Facilitate additional services and programs with third parties; and</li>
                <li>Operate contests, sweepstakes, and other promotions.</li>
                </ul>
                
                <p><strong>Maintaining the Security and Safety of the Mi Softwares Platform and its Users.</strong> Providing you a secure and safe experience drives our platform, both on the road and on our apps. To do this, we use your personal information to:</p>
                
                <ul>
                <li>Authenticate users;</li>
                <li>Verify that Drivers and their vehicles meet safety requirements;</li>
                <li>Investigate and resolve incidents, accidents, and insurance claims;</li>
                <li>Encourage safe driving behavior and avoid unsafe activities;</li>
                <li>Find and prevent fraud; and</li>
                <li>Block and remove unsafe or fraudulent users from the Mi Softwares Platform.</li>
                </ul>
                
                <p><strong>Building and Maintaining the Mi Softwares Community.</strong> Mi Softwares works to be a positive part of the community. We use your personal information to:</p>
                
                <ul>
                <li>Communicate with you about events, promotions, elections, and campaigns;</li>
                <li>Personalize and provide content, experiences, communications, and advertising to promote and grow the Mi Softwares Platform; and</li>
                <li>Help facilitate donations you choose to make through the Mi Softwares Platform.</li>
                </ul>
                
                <p><strong>Providing Customer Support.</strong> We work hard to provide the best experience possible, including supporting you when you need it. To do this, we use your personal information to:</p>
                
                <ul>
                <li>Investigate and assist you in resolving questions or issues you have regarding the Mi Softwares Platform; and</li>
                <li>Provide you support or respond to you.</li>
                </ul>
                
                <p><strong>Improving the Mi Softwares Platform</strong>. We are always working to improve your experience and provide you with new and helpful features. To do this, we use your personal information to:</p>
                
                <ul>
                <li>Perform research, testing, and analysis;</li>
                <li>Develop new products, features, partnerships, and services;</li>
                <li>Prevent, find, and resolve software or hardware bugs and issues; and</li>
                <li>Monitor and improve our operations and processes, including security practices, algorithms, and other modeling.</li>
                </ul>
                
                <p><strong>Responding to Legal Proceedings and Requirements.</strong> Sometimes the law, government entities, or other regulatory bodies impose demands and obligations on us with respect to the services we seek to provide. In such a circumstance, we may use your personal information to respond to those demands or obligations.</p>
                
                <p>How We Share Your Information</p>
                
                <p>We do not sell your personal information. To make the Mi Softwares Platform work, we may need to share your personal information with other users, third parties, and service providers. This section explains when and why we share your information.</p>
                
                <p>A. Sharing Between Mi Softwares Users</p>
                
                <p>Riders and Drivers.<br />
                <br />
                <strong>Rider information shared with Driver:</strong> Upon receiving a ride request, we share with the Driver the Rider&rsquo;s pickup location, name, profile photo, rating, Rider statistics (like approximate number of rides and years as a Rider), and information the Rider includes in their Rider profile (like preferred pronouns). Upon pickup and during the ride, we share with the Driver the Rider&rsquo;s destination and any additional stops the Rider inputs into the Mi Softwares app. Once the ride is finished, we also eventually share the Rider&rsquo;s rating and feedback with the Driver. (We remove the Rider&rsquo;s identity associated with ratings and feedback when we share it with Drivers, but a Driver may be able to identify the Rider that provided the rating or feedback.)<br />
                <br />
                <strong>Driver information shared with Rider:</strong> Upon a Driver accepting a requested ride, we will share with the Rider the Driver&rsquo;s name, profile photo, preferred pronouns, rating, real-time location, and the vehicle make, model, color, and license plate, as well as other information in the Driver&rsquo;s Mi Softwares profile, such as information Drivers choose to add (like country flag and why you drive) and Driver statistics (like approximate number of rides and years as a Driver).<br />
                <br />
                Although we help Riders and Drivers communicate with one another to arrange a pickup, we do not share your actual phone number or other contact information with other users. If you report a lost or found item to us, we will seek to connect you with the relevant Rider or Driver, including sharing actual contact information with your consent.<br />
                <br />
                <strong>Shared Ride Riders.</strong> When Riders use a Mi Softwares Shared ride, we share each Rider&rsquo;s name and profile picture to ensure safety. Riders may also see each other&rsquo;s pickup and drop-off locations as part of knowing the route while sharing the ride.<br />
                <br />
                <strong>Rides Requested or Paid For by Others.</strong> Some rides you take may be requested or paid for by others. If you take one of those rides using your Mi Softwares Business Profile account, a code or coupon, a subsidized program (e.g., transit or government), or a corporate credit card linked to another account, or another user otherwise requests or pays for a ride for you, we may share some or all of your ride details with that other party, including the date, time, charge, rating given, region of trip, and pick up and drop off location of your ride.<br />
                <br />
                <strong>Referral Programs.</strong> If you refer someone to the Mi Softwares Platform, we will let them know that you generated the referral. If another user referred you, we may share information about your use of the Mi Softwares Platform with that user. For example, a referral source may receive a bonus when you join the Mi Softwares Platform or complete a certain number of rides and would receive such information.</p>
                
                <p>B. Sharing With Third-Party Service Providers for Business Purposes</p>
                
                <p>Depending on whether you&rsquo;re a Rider or a Driver, Mi Softwares may share the following categories of your personal information for a business purpose to provide you with a variety of the Mi Softwares Platform&rsquo;s features and services:</p>
                
                <ul>
                <li>Personal identifiers, such as your name, address, email address, phone number, date of birth, government identification number (such as social security number), driver&rsquo;s license information, vehicle information, and car insurance information;</li>
                <li>Financial information, such as bank routing numbers, tax information, and any other payment information you provide us;</li>
                <li>Commercial information, such as ride information, Driver/Rider statistics and feedback, and Driver/Rider transaction history;</li>
                <li>Internet or other electronic network activity information, such as your IP address, type of browser, version of operating system, carrier and/or manufacturer, device identifiers, and mobile advertising identifiers; and</li>
                <li>Location data.</li>
                </ul>
                
                <p>We disclose those categories of personal information to service providers to fulfill the following business purposes:</p>
                
                <ul>
                <li>Maintaining and servicing your Mi Softwares account;</li>
                <li>Processing or fulfilling rides;</li>
                <li>Providing you customer service;</li>
                <li>Processing Rider transactions;</li>
                <li>Processing Driver applications and payments;</li>
                <li>Verifying the identity of users;</li>
                <li>Detecting and preventing fraud;</li>
                <li>Processing insurance claims;</li>
                <li>Providing Driver loyalty and promotional programs;</li>
                <li>Providing marketing and advertising services to Mi Softwares;</li>
                <li>Providing financing;</li>
                <li>Providing requested emergency services;</li>
                <li>Providing analytics services to Mi Softwares; and</li>
                <li>Undertaking internal research to develop the Mi Softwares Platform.</li>
                </ul>
                
                <p>C. For Legal Reasons and to Protect the Mi Softwares Platform</p>
                
                <ul>
                <li>Comply with any applicable federal, state, or local law or regulation, civil, criminal or regulatory inquiry, investigation or legal process, or enforceable governmental request;</li>
                <li>Respond to legal process (such as a search warrant, subpoena, summons, or court order);</li>
                <li>Enforce our Terms of Service;</li>
                <li>Cooperate with law enforcement agencies concerning conduct or activity that we reasonably and in good faith believe may violate federal, state, or local law; or</li>
                <li>Exercise or defend legal claims, protect against harm to our rights, property, interests, or safety or the rights, property, interests, or safety of you, third parties, or the public as required or permitted by law.</li>
                </ul>
                
                <p>D. In Connection with Sale or Merger</p>
                
                <p>We may share your personal information while negotiating or in relation to a change of corporate control such as a restructuring, merger, or sale of our assets.</p>
                
                <p>E. Upon Your Further Direction</p>
                
                <p>With your permission or upon your direction, we may disclose your personal information to interact with a third party or for other purposes.</p>
                
                <p>How We Store and Protect Your Information</p>
                
                <p>We retain your information for as long as necessary to provide you and our other users the Mi Softwares Platform. This means we keep your profile information for as long as you maintain an account. We retain transactional information such as rides and payments for at least seven years to ensure we can perform legitimate business functions, such as accounting for tax obligations. If you request account deletion, we will delete your information as set forth in the &ldquo;Deleting Your Account&rdquo; section below. We take reasonable and appropriate measures designed to protect your personal information. But no security measures can be 100% effective, and we cannot guarantee the security of your information, including against unauthorized intrusions or acts by third parties.</p>
                
                <p>Your Rights And Choices Regarding Your Data</p>
                
                <p>Mi Softwares provides ways for you to access and delete your personal information as well as exercise other data rights that give you certain control over your personal information.</p>
                
                <p>A. All Users</p>
                
                <p>Email Subscriptions. You can always unsubscribe from our commercial or promotional emails by clicking unsubscribe in those messages. We will still send you transactional and relational emails about your use of the Mi Softwares Platform.<br />
                <br />
                <strong>Text Messages.</strong> You can opt out of receiving commercial or promotional text. You may also opt out of receiving all texts from Mi Softwares (including transactional or relational messages. Note that opting out of receiving all texts may impact your use of the Mi Softwares Platform. Drivers can also opt out of driver-specific messages by texting STOP in response to a driver SMS. To re-enable texts you can text START in response to an unsubscribe confirmation SMS.<br />
                <br />
                <strong>Push Notifications.</strong> You can opt out of receiving push notifications through your device settings. Please note that opting out of receiving push notifications may impact your use of the Mi Softwares Platform (such as receiving a notification that your ride has arrived).<br />
                <br />
                <strong>Profile Information</strong>. You can review and edit certain account information you have chosen to add to your profile by logging in to your account settings and profile.<br />
                <br />
                <strong>Location Information.</strong> You can prevent your device from sharing location information through your device&rsquo;s system settings. But if you do, this may impact Mi Softwares&rsquo;s ability to provide you our full range of features and services.<br />
                <br />
                <strong>Cookie Tracking.</strong> You can modify your cookie settings on your browser, but if you delete or choose not to accept our cookies, you may be missing out on certain features of the Mi Softwares Platform.<br />
                <br />
                <strong>Do Not Track.</strong> Your browser may offer you a &ldquo;Do Not Track&rdquo; option, which allows you to signal to operators of websites and web applications and services that you do not want them to track your online activities. The Mi Softwares Platform does not currently support Do Not Track requests at this time.<br />
                <br />
                <strong>Deleting Your Account.</strong> If you would like to delete your Mi Softwares account, please visit our privacy homepage. In some cases, we will be unable to delete your account, such as if there is an issue with your account related to trust, safety, or fraud. When we delete your account, we may retain certain information for legitimate business purposes or to comply with legal or regulatory obligations. For example, we may retain your information to resolve open insurance claims, or we may be obligated to retain your information as part of an open legal claim. When we retain such data, we do so in ways designed to prevent its use for other purposes.<br />
                <br />
                <strong>Right to Know.</strong> You have the right to know and see what data we have collected about, including:</p>
                
                <ul>
                <li>The categories of personal information we have collected about you;</li>
                <li>The categories of sources from which the personal information is collected;</li>
                <li>The business or commercial purpose for collecting your personal information;</li>
                <li>The categories of third parties with whom we have shared your personal information; and</li>
                <li>The specific pieces of personal information we have collected about you.</li>
                </ul>
                
                <p><strong>Right to Delete.</strong> You have the right to request that we delete the personal information we have collected from you (and direct our service providers to do the same). There are a number of exceptions, however, that include, but are not limited to, when the information is necessary for us or a third party to do any of the following:</p>
                
                <ul>
                <li>Complete your transaction;</li>
                <li>Provide you a good or service;</li>
                <li>Perform a contract between us and you;</li>
                <li>Protect your security and prosecute those responsible for breaching it;</li>
                <li>Fix our system in the case of a bug;</li>
                <li>Protect the free speech rights of you or other users;</li>
                <li>Engage in public or peer-reviewed scientific, historical, or statistical research in the public interests that adheres to all other applicable ethics and privacy laws;</li>
                <li>Comply with a legal obligation; or</li>
                <li>Make other internal and lawful uses of the information that are compatible with the context in which you provided it.</li>
                </ul>
                
                <p><strong>Other Rights.</strong> You can request certain information about our disclosure of personal information to third parties for their own direct marketing purposes during the preceding calendar year. This request is free and may be made once a year. You also have the right not to be discriminated against for exercising any of the rights listed above.<br />
                <br />
                <strong>Website:</strong> You may visit our privacy homepage to authenticate and exercise rights via our website.<br />
                <br />
                <strong>Email webform:</strong> You may write to us to exercise rights. To respond to some rights we will need to verify your request either by asking you to log in and authenticate your account or otherwise verify your identity by providing information about yourself or your account. Authorized agents can make a request on your behalf if you have given them legal power of attorney or we are provided proof of signed permission, verification of your identity, and confirmation that you provided the agent permission to submit the request. Response Timing and Format. We aim to respond to a consumer request for access or deletion within 45 days of receiving that request. If we require more time, we will inform you of the reason and extension period in writing.</p>
                
                <p>Children&rsquo;s Data</p>
                
                <p>Mi Softwares is not directed to children, and we don&rsquo;t knowingly collect personal information from children under the age of 13. If we find out that a child under 13 has given us personal information, we will take steps to delete that information. If you believe that a child under the age of 13 has given us personal information, please contact us</p>
                
                <p>Links to Third-Party Websites</p>
                
                <p>The Mi Softwares Platform may contain links to third-party websites. Those websites may have privacy policies that differ from ours. We are not responsible for those websites, and we recommend that you review their policies. Please contact those websites directly if you have any questions about their privacy policies.</p>
                
                <p>Changes to This Privacy Policy</p>
                
                <p>We may update this policy from time to time as the Mi Softwares Platform changes and privacy law evolves. If we update it, we will do so online, and if we make material changes, we will let you know through the Mi Softwares Platform or by some other method of communication like email. When you use Mi Softwares, you are agreeing to the most recent terms of this policy.</p>
                
                <p>Contact Us</p>
                
                <p>If you have any questions or concerns about your privacy or anything in this policy, including if you need to access this policy in an alternative format, we encourage you to contact us.</p>',
            'terms_title' => 'Terms & Conditions',
            'terms' => '<h2><strong>Terms and Conditions</strong></h2>

                <p>END USER LICENSE AGREEMENT</p>

                <p>Last updated May 16, 2021</p>

                <p>Mi Softwares,LLC is licensed to You (End-User) by Mi Softwares, LLC, located at 6255 Towncenter Drive Ste 819, Clemmons, North Carolina 27012, United States (hereinafter: Licensor), for use only under the terms of this License Agreement.<br />
                <br />
                By downloading the Application from the Apple AppStore and Google Play, and any update thereto (as permitted by this License Agreement), You indicate that You agree to be bound by all of the terms and conditions of this License Agreement, and that You accept this License Agreement.<br />
                <br />
                The parties of this License Agreement acknowledge that Apple and/or Google Play is not a Party to this License Agreement and is not bound by any provisions or obligations with regard to the Application, such as warranty, liability, maintenance and support thereof. Mi Softwares, LLC, not Apple or Google Play, is solely responsible for the licensed Application and the content thereof.<br />
                <br />
                This License Agreement may not provide for usage rules for the Application that are in conflict with the latest App Store Terms of Service. Mi Softwares, LLC acknowledges that it had the opportunity to review said terms and this License Agreement is not conflicting with them.<br />
                <br />
                All rights not expressly granted to You are reserved.</p>

                <p>1. THE APPLICATION</p>

                <p>Mi Softwares (hereinafter: Application) is a piece of software is a Rideshare platform - and customized for Apple and Android mobile devices. It is used to Connecting riders to drivers to get to point A to B by a push of a button.<br />
                <br />
                The Application is not tailored to comply with industry-specific regulations (Health Insurance Portability and Accountability Act (HIPAA), Federal Information Security Management Act (FISMA), etc.), so if your interactions would be subjected to such laws, you may not use this Application. You may not use the Application in a way that would violate the Gramm-Leach-Bliley Act (GLBA).</p>

                <p>2. SCOPE OF LICENSE</p>

                <p>2.1 You are given a non-transferable, non-exclusive, non-sublicensable license to install and use the Licensed Application on any Apple-branded or Google Products that You (End-User) own or control and as permitted by the Usage Rules set forth in this section and the App Store Terms of Service, with the exception that such licensed Application may be accessed and used by other accounts associated with You (End-User, The Purchaser) via Family Sharing or volume purchasing.<br />
                <br />
                2.2 This license will also govern any updates of the Application provided by Licensor that replace, repair, and/or supplement the first Application, unless a separate license is provided for such update in which case the terms of that new license will govern.<br />
                <br />
                2.3 You may not share or make the Application available to third parties (unless to the degree allowed by the Apple Terms and Conditions, and with Mi Softwares, LLC&#39;s prior written consent), sell, rent, lend, lease or otherwise redistribute the Application.<br />
                <br />
                2.4 You may not reverse engineer, translate, disassemble, integrate, decompile, integrate, remove, modify, combine, create derivative works or updates of, adapt, or attempt to derive the source code of the Application, or any part thereof (except with Mi Softwares, LLC&#39;s prior written consent).<br />
                <br />
                2.5 You may not copy (excluding when expressly authorized by this license and the Usage Rules) or alter the Application or portions thereof. You may create and store copies only on devices that You own or control for backup keeping under the terms of this license, the App Store Terms of Service, and any other terms and conditions that apply to the device or software used. You may not remove any intellectual property notices. You acknowledge that no unauthorized third parties may gain access to these copies at any time.<br />
                <br />
                2.6 Violations of the obligations mentioned above, as well as the attempt of such infringement, may be subject to prosecution and damages.<br />
                <br />
                2.7 Licensor reserves the right to modify the terms and conditions of licensing.<br />
                <br />
                2.8 Nothing in this license should be interpreted to restrict third-party terms. When using the Application, You must ensure that You comply with applicable third-party terms and conditions.</p>

                <p>3. TECHNICAL REQUIREMENTS</p>

                <p>3.1 Licensor attempts to keep the Application updated so that it complies with modified/new versions of the firmware and new hardware. You are not granted rights to claim such an update.<br />
                <br />
                3.2 You acknowledge that it is Your responsibility to confirm and determine that the app end-user device on which You intend to use the Application satisfies the technical specifications mentioned above.<br />
                <br />
                3.3 Licensor reserves the right to modify the technical specifications as it sees appropriate at any time.</p>

                <p>4. MAINTENANCE AND SUPPORT</p>

                <p>4.1 The Licensor is solely responsible for providing any maintenance and support services for this licensed Application. You can reach the Licensor at the email address listed in the App Store or Google Play Overview for this licensed Application.<br />
                <br />
                4.2 Mi Softwares, LLC and the End-User acknowledge that Apple and or Google Play has no obligation whatsoever to furnish any maintenance and support services with respect to the licensed Application.</p>

                <p>5. USE OF DATA</p>

                <p>You acknowledge that Licensor will be able to access and adjust Your downloaded licensed Application content and Your personal information, and that Licensor&#39;s use of such material and information is subject to Your legal agreements with Licensor and Licensor&#39;s privacy policy: http://www.Mi Softwares.us/privacy.</p>

                <p>6. USER GENERATED CONTRIBUTIONS</p>

                <p>The Application may invite you to chat, contribute to, or participate in blogs, message boards, online forums, and other functionality, and may provide you with the opportunity to create, submit, post, display, transmit, perform, publish, distribute, or broadcast content and materials to us or in the Application, including but not limited to text, writings, video, audio, photographs, graphics, comments, suggestions, or personal information or other material (collectively, &quot;Contributions&quot;). Contributions may be viewable by other users of the Application and through third-party websites or applications. As such, any Contributions you transmit may be treated as non-confidential and non-proprietary. When you create or make available any Contributions, you thereby represent and warrant that:<br />
                <br />
                1. The creation, distribution, transmission, public display, or performance, and the accessing, downloading, or copying of your Contributions do not and will not infringe the proprietary rights, including but not limited to the copyright, patent, trademark, trade secret, or moral rights of any third party.<br />
                <br />
                2. You are the creator and owner of or have the necessary licenses, rights, consents, releases, and permissions to use and to authorize us, the Application, and other users of the Application to use your Contributions in any manner contemplated by the Application and these Terms of Use.<br />
                <br />
                3. You have the written consent, release, and/or permission of each and every identifiable individual person in your Contributions to use the name or likeness or each and every such identifiable individual person to enable inclusion and use of your Contributions in any manner contemplated by the Application and these Terms of Use.<br />
                <br />
                4. Your Contributions are not false, inaccurate, or misleading.<br />
                <br />
                5. Your Contributions are not unsolicited or unauthorized advertising, promotional materials, pyramid schemes, chain letters, spam, mass mailings, or other forms of solicitation.<br />
                <br />
                6. Your Contributions are not obscene, lewd, lascivious, filthy, violent, harassing, libelous, slanderous, or otherwise objectionable (as determined by us).<br />
                <br />
                7. Your Contributions do not ridicule, mock, disparage, intimidate, or abuse anyone.<br />
                <br />
                8. Your Contributions are not used to harass or threaten (in the legal sense of those terms) any other person and to promote violence against a specific person or class of people.<br />
                <br />
                9. Your Contributions do not violate any applicable law, regulation, or rule.<br />
                <br />
                10. Your Contributions do not violate the privacy or publicity rights of any third party.<br />
                <br />
                11. Your Contributions do not contain any material that solicits personal information from anyone under the age of 18 or exploits people under the age of 18 in a sexual or violent manner.<br />
                <br />
                12. Your Contributions do not violate any applicable law concerning child pornography, or otherwise intended to protect the health or well-being of minors.<br />
                <br />
                13. Your Contributions do not include any offensive comments that are connected to race, national origin, gender, sexual preference, or physical handicap.<br />
                <br />
                14. Your Contributions do not otherwise violate, or link to material that violates, any provision of these Terms of Use, or any applicable law or regulation.<br />
                <br />
                Any use of the Application in violation of the foregoing violates these Terms of Use and may result in, among other things, termination or suspension of your rights to use the Application.</p>

                <p>7. CONTRIBUTION LICENSE</p>

                <p>By posting your Contributions to any part of the Application or making Contributions accessible to the Application by linking your account from the Application to any of your social networking accounts, you automatically grant, and you represent and warrant that you have the right to grant, to us an unrestricted, unlimited, irrevocable, perpetual, non-exclusive, transferable, royalty-free, fully-paid, worldwide right, and license to host, use copy, reproduce, disclose, sell, resell, publish, broad cast, retitle, archive, store, cache, publicly display, reformat, translate, transmit, excerpt (in whole or in part), and distribute such Contributions (including, without limitation, your image and voice) for any purpose, commercial advertising, or otherwise, and to prepare derivative works of, or incorporate in other works, such as Contributions, and grant and authorize sublicenses of the foregoing. The use and distribution may occur in any media formats and through any media channels.<br />
                <br />
                This license will apply to any form, media, or technology now known or hereafter developed, and includes our use of your name, company name, and franchise name, as applicable, and any of the trademarks, service marks, trade names, logos, and personal and commercial images you provide. You waive all moral rights in your Contributions, and you warrant that moral rights have not otherwise been asserted in your Contributions.<br />
                <br />
                We do not assert any ownership over your Contributions. You retain full ownership of all of your Contributions and any intellectual property rights or other proprietary rights associated with your Contributions. We are not liable for any statements or representations in your Contributions provided by you in any area in the Application. You are solely responsible for your Contributions to the Application and you expressly agree to exonerate us from any and all responsibility and to refrain from any legal action against us regarding your Contributions.<br />
                <br />
                We have the right, in our sole and absolute discretion, (1) to edit, redact, or otherwise change any Contributions; (2) to re-categorize any Contributions to place them in more appropriate locations in the Application; and (3) to pre-screen or delete any Contributions at any time and for any reason, without notice. We have no obligation to monitor your Contributions.</p>

                <p>8. LIABILITY</p>

                <p>8.1 Licensor&#39;s responsibility in the case of violation of obligations and tort shall be limited to intent and gross negligence. Only in case of a breach of essential contractual duties (cardinal obligations), Licensor shall also be liable in case of slight negligence. In any case, liability shall be limited to the foreseeable, contractually typical damages. The limitation mentioned above does not apply to injuries to life, limb, or health.<br />
                <br />
                8.2 Licensor takes no accountability or responsibility for any damages caused due to a breach of duties according to Section 2 of this Agreement. To avoid data loss, You are required to make use of backup functions of the Application to the extent allowed by applicable third-party terms and conditions of use. You are aware that in case of alterations or manipulations of the Application, You will not have access to licensed Application.</p>

                <p>9. WARRANTY</p>

                <p>9.1 Licensor warrants that the Application is free of spyware, trojan horses, viruses, or any other malware at the time of Your download. Licensor warrants that the Application works as described in the user documentation.<br />
                <br />
                9.2 No warranty is provided for the Application that is not executable on the device, that has been unauthorizedly modified, handled inappropriately or culpably, combined or installed with inappropriate hardware or software, used with inappropriate accessories, regardless if by Yourself or by third parties, or if there are any other reasons outside of Mi Softwares, LLC&#39;s sphere of influence that affect the executability of the Application.<br />
                <br />
                9.3 You are required to inspect the Application immediately after installing it and notify Mi Softwares, LLC about issues discovered without delay by e-mail provided in Product Claims. The defect report will be taken into consideration and further investigated if it has been mailed within a period of ninety (90) days after discovery.<br />
                <br />
                9.4 If we confirm that the Application is defective, Mi Softwares, LLC reserves a choice to remedy the situation either by means of solving the defect or substitute delivery.<br />
                <br />
                9.5 In the event of any failure of the Application to conform to any applicable warranty, You may notify the App-Store-Operator, and Your Application purchase price will be refunded to You. To the maximum extent permitted by applicable law, the App-Store-Operator will have no other warranty obligation whatsoever with respect to the App, and any other losses, claims, damages, liabilities, expenses and costs attributable to any negligence to adhere to any warranty.<br />
                <br />
                9.6 If the user is an entrepreneur, any claim based on faults expires after a statutory period of limitation amounting to twelve (12) months after the Application was made available to the user. The statutory periods of limitation given by law apply for users who are consumers.</p>

                <p>10. PRODUCT CLAIMS</p>

                <p>Mi Softwares, LLC and the End-User acknowledge that Mi Softwares, LLC, and not Apple, is responsible for addressing any claims of the End-User or any third party relating to the licensed Application or the End-User&rsquo;s possession and/or use of that licensed Application, including, but not limited to:<br />
                <br />
                (i) product liability claims;<br />
                <br />
                (ii) any claim that the licensed Application fails to conform to any applicable legal or regulatory requirement; and<br />
                <br />
                (iii) claims arising under consumer protection, privacy, or similar legislation, including in connection with Your Licensed Application&rsquo;s use.</p>

                <p>11. LEGAL COMPLIANCE</p>

                <p>You represent and warrant that You are not located in a country that is subject to a U.S. Government embargo, or that has been designated by the U.S. Government as a &quot;terrorist supporting&quot; country; and that You are not listed on any U.S. Government list of prohibited or restricted parties.</p>

                <p>12. CONTACT INFORMATION</p>

                <p>For general inquiries, complaints, questions or claims concerning the licensed Application, please contact:<br />
                <br />
                <strong>Mi Softwares, LLC<br />
                6255 Towncenter Drive Ste 819<br />
                Clemmons, NC 27012<br />
                United States<br />
                support@Mi Softwares.us </strong></p>

                <p>13. TERMINATION</p>

                <p>The license is valid until terminated by Mi Softwares, LLC or by You. Your rights under this license will terminate automatically and without notice from Mi Softwares, LLC if You fail to adhere to any term(s) of this license. Upon License termination, You shall stop all use of the Application, and destroy all copies, full or partial, of the Application.</p>

                <p>14. THIRD-PARTY TERMS OF AGREEMENTS AND BENEFICIARY</p>

                <p>Mi Softwares, LLC represents and warrants that Mi Softwares, LLC will comply with applicable third-party terms of agreement when using licensed Application.<br />
                <br />
                In Accordance with Section 9 of the &quot;Instructions for Minimum Terms of Developer&#39;s End-User License Agreement,&quot; Apple and Apple&#39;s subsidiaries shall be third-party beneficiaries of this End User License Agreement and - upon Your acceptance of the terms and conditions of this license agreement, Apple will have the right (and will be deemed to have accepted the right) to enforce this End User License Agreement against You as a third-party beneficiary thereof.</p>

                <p>15. INTELLECTUAL PROPERTY RIGHTS</p>

                <p>Mi Softwares, LLC and the End-User acknowledge that, in the event of any third-party claim that the licensed Application or the End-User&#39;s possession and use of that licensed Application infringes on the third party&#39;s intellectual property rights, Mi Softwares, LLC, and not Apple, will be solely responsible for the investigation, defense, settlement and discharge or any such intellectual property infringement claims.</p>

                <p>16. APPLICABLE LAW</p>

                <p>This license agreement is governed by the laws of the State of North Carolina excluding its conflicts of law rules.</p>

                <p>17. MISCELLANEOUS</p>

                <p>17.1 If any of the terms of this agreement should be or become invalid, the validity of the remaining provisions shall not be affected. Invalid terms will be replaced by valid ones formulated in a way that will achieve the primary purpose.<br />
                <br />
                17.2 Collateral agreements, changes and amendments are only valid if laid down in writing. The preceding clause can only be waived in writing.</p>',
            'compliance_title' => 'Compliance',
            'compliance' =>'<h3><strong>Equal Employment Opportunity and Non-Discrimination Policy</strong></h3>

                <h3>I. OVERVIEW &amp; SCOPE</h3>

                <p>Mi Softwares, LLC of 6255 TownCenter Drive Ste 819, Clemmons, North Carolina 27012, has established a Non-Discrimination and Equal Employment Opportunity Policy (&quot;EEO&quot;). This EEO policy applies to all aspects of the relationship between Mi Softwares, LLC and its employees, including, but not limited to, employment, recruitment, advertisements for employment, hiring and firing, compensation, assignment, classification of employees, termination, upgrading, promotions, transfer, training, working conditions, wages and salary administration, and employee benefits and application of policies. These policies apply to independent contractors, temporary employees, all personnel working on the premises, and any other persons or firms doing business for or with Mi Softwares, LLC. Any user found to have violated this prohibition will lose access to the Mi Softwares, LLC platform. Applicable laws in certain jurisdictions may require and/or allow the provision of services by and for the benefit of a specific category of persons. In such jurisdictions, services provided in compliance with these laws and the relevant applicable terms are permissible under this policy.</p>

                <h3>II. POLICIES</h3>

                <p>1. DISCRIMINATION.</p>

                <p>Mi Softwares, LLC shall not tolerate, under any circumstances, without exception, any form of discrimination based on race, creed, religion, color, age, disability, pregnancy, marital status, parental status, sexual orientation, gender expression, gender identity, veteran status, military status, domestic violence victim status, national origin, political affiliation, sex, predisposing genetic characteristics, or geographic location and any other status protected by the law. This list is not exhaustive. For qualified people with disabilities, Mi Softwares, LLC will make every effort to provide reasonable workplace accommodations that comply with applicable laws.</p>

                <p>Discrimination in providing transportation services is strictly prohibited</p>

                <p>Associated drivers and employees are required to know the non-discrimination prohibitions. Mi Softwares, LLC will not tolerate as to public accommodations, which includes taxicab services unlawful discriminatory practice to deny, directly or indirectly, any person the full and equal enjoyment of the goods, services, facilities, privileges, advantages, and accommodations of any place of public accommodations (including taxicab services) wholly or partially for a discriminatory reason based on place of residence or business.</p>

                <p>Prohibited Discriminatory Conduct:</p>

                <p>Mi Softwares, LLC recognizes that associated drivers should never discriminate against certain customers by not picking them up, not taking them where they wish to go or by treating them with less respect based on the protected characteristics or traits listed above. Specific examples of discriminatory conduct, include the following:<br />
                <br />
                Not picking up a passenger on the basis of any protected characteristic or trait, including not picking up a passenger with a service animal&middot; Requesting that a passenger get out of a taxicab on the basis of a protected characteristic or trait &middot; Using derogatory or harassing language on the basis of a protected characteristic or trait &middot; Refusing a pickup in a specific geographic area.</p>

                <p>Geographic Discrimination:</p>

                <p>Mi Softwares, LLC does not tolerate geographic discrimination and recognizes how important it is to take the customer to the requested destination without discriminating against that customer based on where he or she wishes to go. All associated drivers, employees, managers, stakeholders, and agents at Mi Softwares, LLC will comply with these anti-discrimination policies. In some cases, local laws and regulations may provide greater protections than those described in this policy.</p>

                <p>2. HARASSMENT</p>

                <p>Mi Softwares, LLC is committed to providing a work environment that is free from harassment. Any behavior that is unwanted and offensive to the recipient, which creates an intimidating, hostile, or humiliating work environment for that person violates Mi Softwares, LLC&#39;s policy. Harassment can occur between members of the opposite sex or the same sex. Harassment, verbal or non-verbal, explicit or implicit, based on an individual&#39;s sex, race, ethnicity, national origin, age, religion or any other legally protected characteristics will not be tolerated. All employees, including supervisors, other management personnel, and independent contractors, are required to abide by this policy. No person will be adversely affected in employment with Mi Softwares, LLC as a result of bringing complaints of harassment.</p>

                <p>3. SEXUAL HARASSMENT</p>

                <p>Unwelcome sexual advances, requests for sexual favors, and other verbal or physical conduct of a sexual nature constitute harassment when (1) submission to such conduct is made either explicitly or implicitly a term or condition of employment; (2) submission to or rejection of such conduct by an individual is used as a basis for employment decisions, promotion, transfer, selection for training, performance evaluations, benefits, or other terms and conditions of employment; or (3) such conduct has the purpose or effect of creating an intimidating, hostile, or offensive work environment or substantially interferes with an employee&#39;s work performance . Mi Softwares, LLC prohibits inappropriate conduct that is sexual in nature at work, on Company business, or at Company-sponsored events including the following: comments, jokes, degrading language, sexually suggestive objects, books, or any form of media electronic or in print form. Sexual harassment is prohibited whether it is between members of the opposite sex or members of the same sex.</p>

                <p>4. STATEMENT ON AFFIRMATIVE ACTION</p>

                <p>An affirmative action program has been developed where Mi Softwares, LLC seeks to increase the representation and participation of minorities</p>

                <p>5. REPORTING DISCRIMINATION &amp; HARASSMENT</p>

                <p>If an employee feels that he or she has been harassed as described in this policy, they should immediately file grievance with: Grievance Department, 6255 TownCenter Drive, Ste 819, Clemmons NC 27012, or by email at compliance@Mi Softwares.us. Once the matter has been reported it will be promptly investigated and any corrective action will be taken when deemed appropriate. All complaints or unlawful harassment under this policy or otherwise will be handled in as confidential a manner as possible. Timely reporting is encouraged to prevent the re-occurrence of, or otherwise address, the behavior that violates this policy or law. Delays in reporting a complaint can limit the type of effectiveness of a response by Mi Softwares, LLC. The procedure for reporting incidents of discriminatory or harassing behavior is not intended to prevent the right of any employee to seek a remedy under available state or federal law by immediately reporting the matter to the appropriate state or federal agency.</p>

                <p>6. RETALIATION</p>

                <p>Retaliation against any person associated with Mi Softwares, LLC who reports instances of harassment - whether he or she is directly or indirectly involved - is in violation of Mi Softwares, LLC&#39;s policies. All reported incidents are assumed to be made in good faith. Any allegations that are proven false will be treated as a serious matter.</p>

                <p>7. DISCIPLINARY MEASURES FOR HARASSMENT</p>

                <p>Any employee engaging in behavior that violates this policy will be subject to disciplinary action, including the possible termination of employment, whether or not an actual law has been violated.</p>

                <p>8. REMEDIES</p>

                <p>Remedies for any instances of verified employment discrimination, whether caused intentionally or by actions that have a discriminatory effect, may include back pay, hiring, promotion, reinstatement, front pay, reasonable accommodation, or other actions deemed appropriate by Mi Softwares, LLC. Remedies can also include payment of attorney&#39;s fees, expert witness fees, court costs and other applicable legal fees.</p>

                <p>9. POLICY IMPLEMENTATION</p>

                <p>Mi Softwares&rsquo;s CEO, Lynn Graham, fully supports the implementation of this Policy effective as of April 19, 2021.</p>',
            'dmv_title' => 'DMV Check',
            'dmv' =>    '<h2><strong>DMV check &amp; background check consent</strong></h2>

                <p>&nbsp;</p>

                <p>Consent to Request Driving Record</p>

                <p>I understand that Mi Softwares, LLC. (&lsquo;Company&rsquo;) will use Checkr., (&lsquo;Checkr, Inc.&rsquo;) to obtain a motor vehicle record as part of the application process to be a driver on the Mi Softwares Platform (a &lsquo;Driver&rsquo;). I also understand that if accepted as a Driver, to the extent permitted by law, Company may obtain further Reports from Checkr Inc. so as to update, renew or extend my status as a Driver. I hereby give permission to Mi Softwares to obtain my state driving record (also known as my motor vehicle record or MVR) in accordance with the Federal Driver&rsquo;s Privacy Protection Act (&lsquo;DPPA&rsquo;) and applicable state law. I acknowledge and understand that my driving record is a consumer report that contains public record information. I authorize, without reservation any party or agency contacted by Company or Checkr Inc. to furnish Company a copy of my state driving record. This authorization shall remain on file by Company for the duration of my time as a Driver, and will serve as ongoing authorization for Company to procure my state driving record at any time while I am a Driver.</p>

                <p>Consent to Request Consumer Report or Investigative Consumer Report Information</p>

                <p>I understand that Mi Softwares, LLC. (&lsquo;Company&rsquo;) will use Checkr Inc.,</p>

                <p>1 Montgomery St, Ste 2000, San Francisco, CA 94104</p>

                <p>to obtain a consumer report or investigative consumer report as part of the application process to be a driver on the Mi Softwares Platform (a &lsquo;Driver&rsquo;). I also understand that if accepted as a Driver, to the extent permitted by law, Company may obtain further Reports from Checkr so as to update, renew or extend my status as a Driver.</p>

                <p>I understand Checkr, Inc&rsquo;s (&ldquo;Checkr&rdquo;) investigation may include obtaining information regarding my criminal record, subject to any limitations imposed by applicable federal and state law. I understand such information may be obtained through direct or indirect contact with public agencies or other persons who may have such knowledge.</p>

                <p>The nature and scope of the investigation sought will include a Criminal Background check and SSN Trace.</p>

                <p>I acknowledge receipt of the attached summary of my rights under the Fair Credit Reporting Act and, as required by law, any related state summary of rights (collectively &ldquo;Summaries of Rights&rdquo;).</p>

                <p>This consent will not affect my ability to question or dispute the accuracy of any information contained in a Report. I understand if Company makes a conditional decision to disqualify me based all or in part on my Report, I will be provided with a copy of the Report and another copy of the Summaries of Rights, and if I disagree with the accuracy of the purported disqualifying information in the Report, I must notify Company within five business days of my receipt of the Report that I am challenging the accuracy of such information with Checkr.</p>

                <p>I hereby consent to this investigation and authorize Company to procure a Report on my background.</p>

                <p>In order to verify my identity for the purposes of Report preparation, I am voluntarily releasing my date of birth, social security number and the other information and fully understand that all decisions are based on legitimate non-discriminatory reasons.</p>

                <p>The name, address and telephone number of the nearest unit of the consumer reporting agency designated to handle inquiries regarding the investigative consumer report is:</p>

                <p><strong>Checkr, Inc.<br />
                1 Montgomery St, Ste 2000, San Francisco, CA 94104<br />
                844-824-3257 </strong><br />
                <br />
                <strong>California, Maine, Massachusetts, Minnesota, New Jersey &amp; Oklahoma Applicants Only:</strong> I have the right to request a copy of any Report obtained by Company from Checkr by checking the box. (Check only if you wish to receive a copy)</p>

                <p>New York Applicants Only</p>

                <p>I also acknowledge that I have received the attached copy of Article 23A of New York&rsquo;s Correction Law. I further understand that I may request a copy of any investigative consumer report by contacting Checkr. I further understand that I will be advised if any further checks are requested and provided the name and address of the consumer reporting agency.</p>

                <p>California Applicants and Residents</p>

                <p>If I am applying in California or reside in California, I understand I have the right to visually inspect the files concerning me maintained by an investigative consumer reporting agency during normal business hours and upon reasonable notice. The inspection can be done in person, and, if I appear in person and furnish proper identification; I am entitled to a copy of the file for a fee not to exceed the actual costs of duplication. I am entitled to be accompanied by one person of my choosing, who shall furnish reasonable identification. The inspection can also be done via certified mail if I make a written request, with proper identification, for copies to be sent to a specified addressee. I can also request a summary of the information to be provided by telephone if I make a written request, with proper identification for telephone disclosure, and the toll charge, if any, for the telephone call is prepaid by or directly charged to me. I further understand that the investigative consumer reporting agency shall provide trained personnel to explain to me any of the information furnished to me; I shall receive from the investigative consumer reporting agency a written explanation of any coded information contained in files maintained on me. &ldquo;Proper identification&rdquo; as used in this paragraph means information generally deemed sufficient to identify a person, including documents such as a valid driver&rsquo;s license, social security account number, military identification card and credit cards. I understand that I can access the following website checkr.com privacy to view Checkr&rsquo;s privacy practices, including information with respect to Checkr&rsquo;s preparation and processing of investigative consumer reports and guidance as to whether my personal information will be sent outside the United States or its territories.</p>

                <p>A Summary of Your Rights Under the Fair Credit Reporting Act</p>

                <p>The federal Fair Credit Reporting Act (FCRA) promotes the accuracy, fairness, and privacy of information in the files of consumer reporting agencies. There are many types of consumer reporting agencies, including credit bureaus and specialty agencies (such as agencies that sell information about check writing histories, medical records, and rental history records). Here is a summary of your major rights under the FCRA. <strong>For more information, including information about additional rights, go to www.consumerfinance.gov/learnmore or write to:</strong></p>

                <p>Consumer Financial Protection Bureau<br />
                1700 G Street NW, Washington, DC 20552</p>

                <p>&nbsp;</p>

                <ul>
                <li>You must be told if information in your file has been used against you. Anyone who uses a credit report or another type of consumer report to deny your application for credit, insurance, or employment &ndash; or to take another adverse action against you &ndash; must tell you, and must give you the name, address, and phone number of the agency that provided the information.</li>
                <li>You have the right to know what is in your file. You may request and obtain all the information about you in the files of a consumer reporting agency (your &ldquo;file disclosure&rdquo;). You will be required to provide proper identification, which may include your Social Security number. In many cases, the disclosure will be free. You are entitled to a free file disclosure if:
                <ol>
                <li>a person has taken adverse action against you because of information in your credit report;</li>
                <li>you are the victim of identity theft and place a fraud alert in your file;</li>
                <li>your file contains inaccurate information as a result of fraud;</li>
                <li>you are on public assistance;</li>
                <li>you are unemployed but expect to apply for employment within 60 days.</li>
                </ol>
                In addition, all consumers are entitled to one free disclosure every 12 months upon request from each nationwide credit bureau and from nationwide specialty consumer reporting agencies. See www.consumerfinance.gov/learnmore for additional information.</li>
                <li>You have the right to ask for a credit score. Credit scores are numerical summaries of your credit-worthiness based on information from credit bureaus. You may request a credit score from consumer reporting agencies that create scores or distribute scores used in residential real property loans, but you will have to pay for it. In some mortgage transactions, you will receive credit score information for free from the mortgage lender.</li>
                <li>You have the right to dispute incomplete or inaccurate information. If you identify information in your file that is incomplete or inaccurate, and report it to the consumer reporting agency, the agency must investigate unless your dispute is frivolous. See www.consumerfinance.gov/learnmore for an explanation of dispute procedures.</li>
                <li>Consumer reporting agencies must correct or delete inaccurate, incomplete, or unverifiable information. Inaccurate, incomplete or unverifiable information must be removed or corrected, usually within 30 days. However, a consumer reporting agency may continue to report information it has verified as accurate.</li>
                <li>Consumer reporting agencies may not report outdated negative information. In most cases, a consumer reporting agency may not report negative information that is more than seven years old, or bankruptcies that are more than 10 years old.</li>
                <li>Access to your file is limited. A consumer reporting agency may provide information about you only to people with a valid need &ndash; usually to consider an application with a creditor, insurer, employer, landlord, or other business. The FCRA specifies those with a valid need for access.</li>
                <li>You must give your consent for reports to be provided to employers. A consumer reporting agency may not give out information about you to your employer, or a potential employer, without your written consent given to the employer. Written consent generally is not required in the trucking industry. For more information, go to www.consumerfinance.gov/learnmore</li>
                <li>You may limit &ldquo;prescreened&rdquo; offers of credit and insurance you get based on information in your credit report. Unsolicited &ldquo;prescreened&rdquo; offers for credit and insurance must include a toll-free phone number you can call if you choose to remove your name and address from the lists these offers are based on. You may opt-out with the nationwide credit bureaus at 1-888-567-8688.</li>
                <li>You may seek damages from violators. If a consumer reporting agency, or, in some cases, a user of consumer reports or a furnisher of information to a consumer reporting agency violates the FCRA, you may be able to sue in state or federal court.</li>
                <li>Identity theft victims and active duty military personnel have additional rights. For more information, visit www.consumerfinance.gov/learnmore.</li>
                </ul>

                <p>States may enforce the FCRA, and many states have their own consumer reporting laws. In some cases, you may have more rights under state law. For more information, contact your state or local consumer protection agency or your state Attorney General. For information about your federal rights, contact:</p>

                <p>&nbsp;</p>

                <table>
                <thead>
                <tr>
                <th>
                <p>Type of business</p>
                </th>
                <th>
                <p>Contact</p>
                </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                <td>1.a. Banks, savings associations, and credit unions with total assets of over $10 billion and their affiliates.</td>
                <td>a. Consumer Financial Protection Bureau 1700 G Street NW, Washington, DC 20552</td>
                </tr>
                <tr>
                <td>1.b. Such affiliates that are not banks, savings associations, or credit unions also should list, in addition to the CFPB:</td>
                <td>b. Federal Trade Commission: Consumer Response Center &ndash; FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td colspan="2">
                <p>To the extent not included in item 1 above</p>
                </td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>2.a. National banks, federal savings associations, and federal branches and federal agencies of foreign banks</td>
                <td>a. Office of the Comptroller of the Currency Customer Assistance Group 1301 McKinney Street Suite 3450, Houston, TX 77010-9050</td>
                </tr>
                <tr>
                <td>2.b. State member banks, branches and agencies of foreign banks (other than federal branches, federal agencies, and Insured State Branches of Foreign Banks), commercial lending companies owned or controlled by foreign banks, and organizations operating under section 25 or 25A of the Federal Reserve Act</td>
                <td>b. Federal Reserve Consumer Help Center P.O. Box 1200 Minneapolis, MN 55480</td>
                </tr>
                <tr>
                <td>2.c. Nonmember Insured Banks, Insured State Branches of Foreign Banks, and insured state savings associations</td>
                <td>c. FDIC Consumer Response Center 1100 Walnut Street Box #11, Kansas City, MO 64106</td>
                </tr>
                <tr>
                <td>2.d. Federal Credit Unions</td>
                <td>d. National Credit Union Administration Office of Consumer Protection (OCP), Division of Consumer Compliance and Outreach (DCCO) 1775 Duke Street, Alexandria, VA 22314</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>3. Air carriers</td>
                <td>Asst. General Counsel for Aviation Enforcement &amp; Proceedings Aviation Consumer Protection Division Department of Transportation 1200 New Jersey Avenue SE, Washington, DC 20590</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>4. Creditors Subject to Surface Transportation Board</td>
                <td>Office of Proceedings, Surface Transportation Board, Department of Transportation 395 E Street SW, Washington, DC 20423</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>5. Creditors Subject to Packers and Stockyards Act, 1921</td>
                <td>Nearest Packers and Stockyards Administration area supervisor</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>6. Small Business Investment Companies</td>
                <td>Associate Deputy Administrator for Capital Access, United States Small Business Administration 409 Third Street SW 8th Floor, Washington, DC 20416</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>7. Brokers and Dealers</td>
                <td>Securities and Exchange Commission 100 F St NE, Washington, DC 20549</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>8. Federal Land Banks, Federal Land Bank Associations, Federal Intermediate Credit Banks, and Production Credit Associations</td>
                <td>Farm Credit Administration, 1501 Farm Credit Drive, McLean, VA 22102-5090</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>9. Retailers, Finance Companies, and All Other Creditors Not Listed Above</td>
                <td>FTC Regional Office for region in which the creditor operates or Federal Trade Commission: Consumer Response Center &ndash; FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                </table>',
            'locale' => 'En',
            'language' => 'English',
            'direction' => 'ltr',
            
        ],
        // arabic
        [
            'id' => Str::uuid(),
            'privacy_title' => 'سياسة الخصوصية',
            'privacy' =>'<h2>سياسة الخصوصية</h2>

                <p>نطاق هذه السياسة</p>
                
                <p>تنطبق هذه السياسة على جميع مستخدمي Mi Softwares، بما في ذلك الركاب والسائقين (بما في ذلك المتقدمين للسائقين)، وعلى جميع منصات وخدمات Mi Softwares، بما في ذلك تطبيقاتنا ومواقعنا الإلكترونية وميزاتنا والخدمات الأخرى (يُشار إليها إجمالاً باسم "منصة Mi Softwares" "). يرجى تذكر أن استخدامك لمنصة Mi Softwares يخضع أيضًا لشروط الخدمة الخاصة بنا.</p>
                
                <p>المعلومات التي نجمعها</p>
                
                <p>عند استخدامك منصة Mi Softwares، نقوم بجمع المعلومات التي تقدمها ومعلومات الاستخدام والمعلومات المتعلقة بجهازك. نقوم أيضًا بجمع معلومات عنك من مصادر أخرى مثل خدمات الجهات الخارجية، والبرامج الاختيارية التي تشارك فيها، والتي قد ندمجها مع المعلومات الأخرى المتوفرة لدينا عنك. فيما يلي أنواع المعلومات التي نجمعها عنك:</p>
                
                <p>أ. المعلومات التي تقدمها لنا</p>
                
                <p>تسجيل الحساب. عندما تقوم بإنشاء حساب مع Mi Softwares، فإننا نجمع المعلومات التي تقدمها لنا، مثل اسمك وعنوان بريدك الإلكتروني ورقم هاتفك ومعلومات الدفع. يمكنك اختيار مشاركة معلومات إضافية معنا بشأن ملف تعريف الراكب الخاص بك، مثل صورتك أو عناوينك المحفوظة (على سبيل المثال، المنزل أو العمل)، وإعداد تفضيلات أخرى (مثل الضمائر المفضلة لديك).<br />
                <br />
                <strong>معلومات السائق.</strong> إذا تقدمت بطلب لتصبح سائقًا، فسنجمع المعلومات التي تقدمها في طلبك، بما في ذلك اسمك وعنوان بريدك الإلكتروني ورقم هاتفك وتاريخ ميلادك وصورة ملفك الشخصي وعنوانك الفعلي وهويتك الحكومية الرقم (مثل رقم الضمان الاجتماعي)، ومعلومات رخصة القيادة، ومعلومات السيارة، ومعلومات التأمين على السيارة. نقوم بجمع معلومات الدفع التي تقدمها لنا، بما في ذلك أرقام توجيه البنك الخاص بك والمعلومات الضريبية. اعتمادًا على المكان الذي تريد القيادة فيه، قد نطلب أيضًا ترخيصًا تجاريًا إضافيًا أو معلومات تصريح أو معلومات أخرى لإدارة القيادة والبرامج ذات الصلة بهذا الموقع. قد نحتاج إلى معلومات إضافية منك في مرحلة ما بعد أن تصبح سائقًا، بما في ذلك معلومات لتأكيد هويتك (مثل الصورة).<br />
                <br />
                <strong>معلومات اتصال SOS</strong> سنقوم بجمع إذن قائمة جهات الاتصال لإدراج جهات الاتصال أثناء إضافة جهات اتصال SOS لتطبيق المستخدم والسائق<br />
                <br />
                <strong>التقييمات والتعليقات.</strong> عندما تقوم بالتقييم وتقديم تعليقات حول الركاب أو السائقين، فإننا نجمع كل المعلومات التي تقدمها في تعليقاتك.<br />
                <br />
                <strong>الاتصالات.</strong> عندما تتصل بنا أو نتصل بك، فإننا نجمع أي معلومات تقدمها، بما في ذلك محتويات الرسائل أو المرفقات التي ترسلها إلينا.</p>
                
                <p>ب. المعلومات التي نجمعها عند استخدامك لمنصة Mi Softwares</p>
                
                <p><strong>معلومات الموقع.</strong> تبدأ الرحلات الرائعة بعملية التقاط سهلة ودقيقة. تقوم منصة Mi Softwares بجمع معلومات الموقع (بما في ذلك بيانات GPS وWiFi) بشكل مختلف اعتمادًا على إعدادات تطبيق Mi Softwares وأذونات الجهاز بالإضافة إلى ما إذا كنت تستخدم النظام الأساسي كراكب أو سائق:</p>
                
                <ul>
                <li>الركاب: نقوم بجمع الموقع الدقيق لجهازك عند فتح تطبيق Mi Softwares واستخدامه، بما في ذلك أثناء تشغيل التطبيق في الخلفية من وقت طلب المشوار وحتى انتهائه. تقوم Mi Softwares أيضًا بتتبع الموقع الدقيق للدراجات البخارية والدراجات الإلكترونية في جميع الأوقات.</li>
                <li>برامج التشغيل: نقوم بجمع الموقع الدقيق لجهازك عند فتح التطبيق واستخدامه، بما في ذلك أثناء تشغيل التطبيق في الخلفية عندما يكون في وضع برنامج التشغيل. نقوم أيضًا بجمع الموقع الدقيق لفترة محدودة بعد الخروج من وضع السائق من أجل اكتشاف حوادث الركوب، ونستمر في جمعها حتى تصبح الحادثة التي تم الإبلاغ عنها أو المكتشفة نشطة.</li>
                </ul>
                
                <p><strong>معلومات الاستخدام.</strong> نحن نجمع معلومات حول استخدامك لمنصة Mi Softwares، بما في ذلك معلومات الرحلة مثل التاريخ والوقت والوجهة والمسافة والمسار والدفع وما إذا كنت استخدمت عرضًا ترويجيًا أو إحالة. شفرة. نقوم أيضًا بجمع معلومات حول تفاعلاتك مع منصة Mi Softwares مثل تطبيقاتنا ومواقعنا الإلكترونية، بما في ذلك الصفحات والمحتوى الذي تشاهده وتواريخ وأوقات استخدامك.<br />
                <br />
                <strong>معلومات الجهاز.</strong> نقوم بجمع معلومات حول الأجهزة التي تستخدمها للوصول إلى منصة Mi Softwares، بما في ذلك طراز الجهاز وعنوان IP ونوع المتصفح وإصدار نظام التشغيل وهوية الناقل والشركة المصنعة ونوع الراديو ( مثل 4G)، والتفضيلات والإعدادات (مثل اللغة المفضلة)، وعمليات تثبيت التطبيقات، ومعرفات الأجهزة، ومعرفات الإعلانات، والرموز المميزة لإشعارات الدفع. إذا كنت سائقًا، فإننا نجمع أيضًا بيانات مستشعرات الهاتف المحمول من جهازك (مثل السرعة والاتجاه والارتفاع والتسارع والتباطؤ والبيانات الفنية الأخرى).<br />
                <br />
                <strong>الاتصالات بين الركاب والسائقين.</strong> نحن نعمل مع طرف ثالث لتسهيل المكالمات الهاتفية والرسائل النصية بين الركاب والسائقين دون مشاركة رقم الهاتف الفعلي لأي من الطرفين مع الآخر. ولكن بينما نستخدم طرفًا ثالثًا لتقديم خدمة الاتصال، فإننا نقوم بجمع معلومات حول هذه الاتصالات، بما في ذلك أرقام هواتف المشاركين والتاريخ والوقت ومحتويات الرسائل النصية القصيرة. ولأغراض أمنية، يجوز لنا أيضًا مراقبة أو تسجيل محتويات المكالمات الهاتفية التي يتم إجراؤها من خلال منصة Mi Softwares، لكننا سنخبرك دائمًا بأننا على وشك القيام بذلك قبل بدء المكالمة.<br />
                <br />
                <strong>جهات اتصال دفتر العناوين.</strong> يمكنك تعيين أذونات جهازك لمنح Mi Softwares حق الوصول إلى قوائم جهات الاتصال الخاصة بك وتوجيه Mi Softwares للوصول إلى قائمة جهات الاتصال الخاصة بك، على سبيل المثال، لمساعدتك في إحالة الأصدقاء إلى Mi Softwares. إذا قمت بذلك، فسنقوم بالوصول إلى الأسماء ومعلومات الاتصال الخاصة بالأشخاص الموجودين في دفتر العناوين الخاص بك وتخزينها.<br />
                <br />
                <strong>ملفات تعريف الارتباط والتحليلات وتقنيات الطرف الثالث.</strong> نحن نجمع المعلومات من خلال استخدام "ملفات تعريف الارتباط" وتتبع وحدات البكسل وأدوات تحليل البيانات مثل Google Analytics وحزم تطوير البرامج (SDK) وتقنيات الطرف الثالث الأخرى لفهم كيفية استخدامك قم بالتنقل عبر منصة Mi Softwares Platform والتفاعل مع إعلانات Mi Softwares، لجعل تجربتك مع Mi Softwares أكثر أمانًا، ولمعرفة المحتوى الشائع، ولتحسين تجربة موقعك، ولتقديم إعلانات أفضل لك على المواقع الأخرى، ولحفظ تفضيلاتك. ملفات تعريف الارتباط هي ملفات نصية صغيرة تضعها خوادم الويب على جهازك؛ وهي مصممة لتخزين المعلومات الأساسية ولمساعدة مواقع الويب والتطبيقات في التعرف على متصفحك. يجوز لنا استخدام ملفات تعريف الارتباط الخاصة بالجلسة وملفات تعريف الارتباط الدائمة. يختفي ملف تعريف ارتباط الجلسة بعد إغلاق المتصفح. يبقى ملف تعريف الارتباط الدائم بعد إغلاق المتصفح الخاص بك ويمكن الوصول إليه في كل مرة تستخدم فيها منصة Mi Softwares. يجب عليك استشارة متصفح (متصفحات) الويب الخاص بك لتعديل إعدادات ملفات تعريف الارتباط الخاصة بك. يرجى ملاحظة أنه إذا قمت بحذف ملفات تعريف الارتباط من جانبنا أو اخترت عدم قبولها، فقد تفوتك بعض ميزات منصة Mi Softwares Platform.</p>
                
                <p>ج. المعلومات التي نجمعها من أطراف ثالثة</p>
                
                <p><strong>خدمات الطرف الثالث.</strong> تزودنا خدمات الطرف الثالث بالمعلومات اللازمة للجوانب الأساسية لمنصة Mi Softwares، بالإضافة إلى الخدمات الإضافية والبرامج ومزايا الولاء والعروض الترويجية التي يمكن أن تعزز تجربة برامج Mi الخاصة بك. تشمل خدمات الطرف الثالث هذه موفري فحص الخلفية، وشركاء التأمين، ومقدمي الخدمات المالية، ومقدمي التسويق، والشركات الأخرى. نحصل على المعلومات التالية عنك من خدمات الطرف الثالث هذه:</p>
                
                <ul>
                <li>معلومات لجعل منصة Mi Softwares Platform أكثر أمانًا، مثل معلومات التحقق من الخلفية للسائقين؛</li>
                <li>معلومات حول مشاركتك في برامج الجهات الخارجية التي توفر أشياء مثل التغطية التأمينية والأدوات المالية، مثل التأمين والدفع والمعاملات ومعلومات الكشف عن الاحتيال؛</li>
                <li>معلومات لتشغيل برامج الولاء والبرامج الترويجية أو التطبيقات أو الخدمات أو الميزات التي تختار الاتصال بها أو ربطها بحساب Mi Softwares الخاص بك، مثل المعلومات حول استخدامك لهذه البرامج أو التطبيقات أو الخدمات أو الميزات؛ و</لي>
                <li>معلومات عنك مقدمة من خدمات محددة، مثل المعلومات الديموغرافية ومعلومات قطاعات السوق.</li>
                </ul>
                
                <p><strong>برامج المؤسسات.</strong> إذا كنت تستخدم برامج Mi من خلال صاحب العمل الخاص بك أو مؤسسة أخرى تشارك في أحد برامج المؤسسات الخاصة بـ Mi Softwares Business، فسنقوم بجمع معلومات عنك من تلك الأطراف، مثل اسمك ومعلومات الاتصال.<br />
                <br />
                <strong>خدمة الكونسيرج.</strong> في بعض الأحيان قد تطلب منك شركة أو كيان آخر رحلة من Mi Softwares. إذا طلبت إحدى المؤسسات رحلة لك باستخدام خدمة الكونسيرج لدينا، فسوف تزودنا بمعلومات الاتصال الخاصة بك وموقع الركوب والتوصيل لرحلتك.<br />
                <br />
                <strong>برامج الإحالة.</strong> يساعد الأصدقاء أصدقاءهم في استخدام منصة Mi Softwares. إذا قام شخص ما بإحالتك إلى Mi Softwares، فسنجمع معلومات عنك من تلك الإحالة بما في ذلك اسمك ومعلومات الاتصال الخاصة بك.<br />
                <br />
                <strong>المستخدمون والمصادر الأخرى.</strong> قد يزودنا المستخدمون الآخرون أو المصادر العامة أو الجهات الخارجية مثل جهات إنفاذ القانون أو شركات التأمين أو وسائل الإعلام أو المشاة بمعلومات عنك، على سبيل المثال كجزء من التحقيق في حادث أو لنقدم لك الدعم.</p>
                
                <p>كيف نستخدم معلوماتك</p>
                
                <p>نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>توفير منصة Mi Softwares؛</li>
                <li>الحفاظ على أمن وسلامة منصة Mi Softwares ومستخدميها؛</li>
                <li>إنشاء وصيانة مجتمع Mi Softwares؛</li>
                <li>تقديم دعم العملاء؛</li>
                <li>تحسين منصة برامج Mi؛ و</لي>
                <li>الرد على الإجراءات والالتزامات القانونية.</li>
                </ul>
                
                <p><strong>توفير منصة Mi Softwares.</strong> نحن نستخدم معلوماتك الشخصية لتوفير تجربة بديهية ومفيدة وفعالة وجديرة بالاهتمام على منصتنا. وللقيام بذلك، نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>التحقق من هويتك والحفاظ على حسابك وإعداداتك وتفضيلاتك؛</li>
                <li>ربطك برحلاتك وتتبع تقدمها؛</li>
                <li>حساب الأسعار ومعالجة المدفوعات؛</li>
                <li>السماح للركاب والشركاء السائقين بالتواصل فيما يتعلق برحلتهم واختيار مشاركة موقعهم مع الآخرين؛</li>
                <li>التواصل معك بشأن رحلاتك وتجربتك؛</li>
                <li>جمع التعليقات المتعلقة بتجربتك؛</li>
                <li>تسهيل الخدمات والبرامج الإضافية مع أطراف ثالثة؛ و</لي>
                <li>إدارة المسابقات واليانصيب والعروض الترويجية الأخرى.</li>
                </ul>
                
                <p><strong>الحفاظ على أمن وسلامة منصة Mi Softwares ومستخدميها.</strong> إن توفير تجربة آمنة ومأمونة لك هو ما يدفع نظامنا الأساسي، سواء على الطريق أو على تطبيقاتنا. وللقيام بذلك، نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>مصادقة المستخدمين؛</li>
                <li>التحقق من أن السائقين ومركباتهم يستوفون متطلبات السلامة؛</li>
                <li>التحقيق في الحوادث والحوادث ومطالبات التأمين وحلها؛</li>
                <li>تشجيع سلوك القيادة الآمنة وتجنب الأنشطة غير الآمنة؛</li>
                <li>البحث عن الاحتيال ومنعه؛ و</لي>
                <li>حظر وإزالة المستخدمين غير الآمنين أو المحتالين من منصة Mi Softwares.</li>
                </ul>
                
                <p><strong>بناء مجتمع Mi Softwares والحفاظ عليه.</strong> تعمل Mi Softwares على أن تكون جزءًا إيجابيًا من المجتمع. نحن نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>التواصل معك بشأن الأحداث والعروض الترويجية والانتخابات والحملات؛</li>
                <li>تخصيص المحتوى والخبرات والاتصالات والإعلانات وتوفيرها للترويج لمنصة Mi Softwares وتنميتها؛ و</لي>
                <li>ساعد في تسهيل التبرعات التي تختار تقديمها من خلال منصة Mi Softwares.</li>
                </ul>
                
                <p><strong>توفير دعم العملاء.</strong> نحن نعمل جاهدين لتوفير أفضل تجربة ممكنة، بما في ذلك تقديم الدعم لك عندما تحتاج إليه. وللقيام بذلك، نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>التحقيق ومساعدتك في حل الأسئلة أو المشكلات التي تواجهك فيما يتعلق بمنصة Mi Softwares Platform؛ و</لي>
                <li>نقدم لك الدعم أو الرد عليك.</li>
                </ul>
                
                <p><strong>تحسين النظام الأساسي لبرامج Mi</strong>. نحن نعمل دائمًا على تحسين تجربتك وتزويدك بميزات جديدة ومفيدة. وللقيام بذلك، نستخدم معلوماتك الشخصية من أجل:</p>
                
                <ul>
                <li>إجراء البحث والاختبار والتحليل؛</li>
                <li>تطوير منتجات وميزات وشراكات وخدمات جديدة؛</li>
                <li>منع الأخطاء والمشكلات المتعلقة بالبرامج أو الأجهزة والعثور عليها وحلها؛ و</لي>
                <li>مراقبة وتحسين عملياتنا وعملياتنا، بما في ذلك ممارسات الأمان والخوارزميات والنماذج الأخرى.</li>
                </ul>
                
                <p><strong>الاستجابة للإجراءات والمتطلبات القانونية.</strong> في بعض الأحيان يفرض القانون أو الهيئات الحكومية أو الهيئات التنظيمية الأخرى مطالب والتزامات علينا فيما يتعلق بالخدمات التي نسعى إلى تقديمها. في مثل هذه الظروف، قد نستخدم معلوماتك الشخصية للرد على تلك المطالب أو الالتزامات.</p>
                
                <p>كيف نشارك معلوماتك</p>
                
                <p>نحن لا نبيع معلوماتك الشخصية. لكي تعمل منصة Mi Softwares Platform، قد نحتاج إلى مشاركة معلوماتك الشخصية مع مستخدمين آخرين، وأطراف ثالثة، ومقدمي الخدمات. يشرح هذا القسم متى ولماذا نشارك معلوماتك.</p>
                
                <p>أ. المشاركة بين مستخدمي برامج Mi</p>
                
                <p>الركاب والسائقون.<br />
                <br />
                <strong>مشاركة معلومات الراكب مع السائق:</strong> عند تلقي طلب رحلة، نشارك مع السائق موقع الالتقاء والاسم وصورة الملف الشخصي والتقييم وإحصائيات الراكب (مثل العدد التقريبي للركوب وسنوات الراكب) والمعلومات التي يدرجها الراكب في ملفه الشخصي (مثل الضمائر المفضلة). عند الالتقاط وأثناء الرحلة، نشارك مع السائق وجهة الراكب وأي توقفات إضافية يُدخلها الراكب في تطبيق Mi Softwares. بمجرد الانتهاء من الرحلة، نقوم أيضًا في النهاية بمشاركة تقييم الراكب وملاحظاته مع السائق. (نقوم بإزالة هوية الراكب المرتبطة بالتقييمات والتعليقات عندما نشاركها مع السائقين، ولكن قد يتمكن السائق من التعرف على الراكب الذي قدم التقييم أو التعليقات.)<br />
                <br />
                <strong>مشاركة معلومات السائق مع الراكب:</strong> عند قبول السائق للرحلة المطلوبة، سنشارك مع الراكب اسم السائق وصورة الملف الشخصي والضمائر المفضلة والتقييم والموقع في الوقت الفعلي ونوع السيارة وطرازها. واللون ولوحة الترخيص، بالإضافة إلى معلومات أخرى في ملف تعريف برنامج Mi Software الخاص بالسائق، مثل المعلومات التي يختار السائقون إضافتها (مثل علم الدولة وسبب قيادتك) وإحصائيات السائق (مثل العدد التقريبي للركوب وسنوات القيادة) .<br />
                <br />
                على الرغم من أننا نساعد الركاب والشركاء السائقين على التواصل مع بعضهم البعض لترتيب عملية النقل، إلا أننا لا نشارك رقم هاتفك الفعلي أو معلومات الاتصال الأخرى مع مستخدمين آخرين. إذا أبلغتنا عن عنصر مفقود أو تم العثور عليه، فسنسعى إلى توصيلك بالراكب أو السائق المعني، بما في ذلك مشاركة معلومات الاتصال الفعلية بموافقتك.<br />
                <br />
                <strong>الراكبون المشتركون في الرحلة.</strong> عندما يستخدم الركاب رحلة مشتركة من Mi Softwares، فإننا نشارك اسم كل راكب وصورة ملفه الشخصي لضمان سلامته. وقد يرى الركاب أيضًا مواقع الركوب والنزول لبعضهم البعض كجزء من معرفة المسار أثناء مشاركة الرحلة.<br />
                <br />
                <strong>الرحلات التي يطلبها الآخرون أو يدفع ثمنها.</strong> قد يتم طلب بعض الرحلات التي تقوم بها أو دفع ثمنها من قبل الآخرين. إذا قمت بإجراء إحدى هذه الرحلات باستخدام حساب Mi Softwares Business Profile الخاص بك، أو رمز أو قسيمة، أو برنامج مدعوم (على سبيل المثال، النقل أو الحكومة)، أو بطاقة ائتمان شركة مرتبطة بحساب آخر، أو يطلب مستخدم آخر أو يدفع مقابل ذلك بالنسبة لك، قد نشارك بعض أو كل تفاصيل رحلتك مع هذا الطرف الآخر، بما في ذلك التاريخ والوقت والتكلفة والتقييم المقدم ومنطقة الرحلة وموقع الركوب والتوصيل لرحلتك.<br />
                <br />
                <strong>برامج الإحالة.</strong> إذا قمت بإحالة شخص ما إلى منصة Mi Softwares، فسنخبره بأنك قمت بإنشاء الإحالة. إذا أحالك مستخدم آخر، فقد نشارك معلومات حول استخدامك لمنصة Mi Softwares مع هذا المستخدم. على سبيل المثال، قد يحصل مصدر الإحالة على مكافأة عند انضمامك إلى Mi Softwares Platform أو إكمال عدد معين من الرحلات وسيتلقى هذه المعلومات.</p>
                
                <p>ب. المشاركة مع موفري الخدمات الخارجيين لأغراض تجارية</p>
                
                <p>اعتمادًا على ما إذا كنت راكبًا أو سائقًا، قد تقوم Mi Softwares بمشاركة الفئات التالية من معلوماتك الشخصية لغرض تجاري لتزويدك بمجموعة متنوعة من ميزات وخدمات Mi Softwares Platform:</p>
                
                <ul>
                <li>المعرفات الشخصية، مثل اسمك وعنوانك وعنوان بريدك الإلكتروني ورقم هاتفك وتاريخ ميلادك ورقم التعريف الحكومي (مثل رقم الضمان الاجتماعي)، ومعلومات رخصة القيادة، ومعلومات السيارة، ومعلومات تأمين السيارة؛</li>
                <li>المعلومات المالية، مثل أرقام توجيه البنك، والمعلومات الضريبية، وأي معلومات دفع أخرى تقدمها لنا؛</li>
                <li>المعلومات التجارية، مثل معلومات الرحلة، وإحصائيات وتعليقات السائق/الراكب، وسجل معاملات السائق/الراكب؛</li>
                <li>معلومات نشاط الإنترنت أو الشبكة الإلكترونية الأخرى، مثل عنوان IP الخاص بك، ونوع المتصفح، وإصدار نظام التشغيل، وشركة الاتصالات و/أو الشركة المصنعة، ومعرفات الجهاز، ومعرفات إعلانات الهاتف المحمول؛ و</لي>
                <li>بيانات الموقع.</li>
                </ul>
                
                <p>نحن نكشف عن فئات المعلومات الشخصية هذه لمقدمي الخدمات لتحقيق الأغراض التجارية التالية:</p>
                
                <ul>
                <li>صيانة وخدمة حساب Mi Softwares الخاص بك؛</li>
                <li>معالجة المشاوير أو استيفائها؛</li>
                <li>توفير خدمة العملاء لك؛</li>
                <li>معالجة معاملات الراكب؛</li>
                <li>معالجة طلبات السائق والمدفوعات؛</li>
                <li>التحقق من هوية المستخدمين؛</li>
                <li>كشف الاحتيال ومنعه؛</li>
                <li>معالجة مطالبات التأمين؛</li>
                <li>توفير برامج الولاء والبرامج الترويجية للسائقين</li>
                <li>تقديم خدمات التسويق والإعلان لشركة Mi Softwares؛</li>
                <li>توفير التمويل؛</li>
                <li>تقديم خدمات الطوارئ المطلوبة؛</li>
                <li>توفير خدمات التحليلات لشركة Mi Softwares؛ و</لي>
                <li>إجراء بحث داخلي لتطوير منصة Mi Softwares.</li>
                </ul>
                
                <p>ج. لأسباب قانونية ولحماية منصة برامج Mi</p>
                
                <ul>
                <li>الامتثال لأي قانون أو لائحة فيدرالية أو خاصة بالولاية أو محلية معمول بها، أو تحقيق مدني أو جنائي أو تنظيمي، أو تحقيق أو إجراء قانوني، أو طلب حكومي واجب النفاذ؛</li>
                <li>الرد على الإجراءات القانونية (مثل أمر التفتيش، أو أمر الاستدعاء، أو الاستدعاء، أو أمر المحكمة)؛</li>
                <li>فرض شروط الخدمة لدينا؛</li>
                <li>التعاون مع وكالات إنفاذ القانون فيما يتعلق بالسلوك أو النشاط الذي نعتقد بشكل معقول وبحسن نية أنه قد ينتهك القانون الفيدرالي أو قانون الولاية أو القانون المحلي؛ أو</li>
                <li>ممارسة المطالبات القانونية أو الدفاع عنها، والحماية من الإضرار بحقوقنا أو ممتلكاتنا أو مصالحنا أو سلامتنا أو حقوقنا أو ممتلكاتنا أو مصالحنا أو سلامتك أو سلامة الأطراف الثالثة أو الجمهور وفقًا لما يقتضيه القانون أو يسمح به.</ لى>
                </ul>
                
                <p>د. فيما يتعلق بالبيع أو الاندماج</p>
                
                <p>يجوز لنا مشاركة معلوماتك الشخصية أثناء التفاوض أو فيما يتعلق بتغيير سيطرة الشركة مثل إعادة الهيكلة أو الدمج أو بيع أصولنا.</p>
                
                <p>ه. بناءً على توجيهاتك الإضافية</p>
                
                <p>بإذن منك أو بناءً على توجيهاتك، قد نكشف عن معلوماتك الشخصية للتفاعل مع طرف ثالث أو لأغراض أخرى.</p>
                
                <p>كيف نقوم بتخزين معلوماتك وحمايتها</p>
                
                <p>نحن نحتفظ بمعلوماتك طالما كان ذلك ضروريًا لتزويدك ولمستخدمينا الآخرين بمنصة Mi Softwares. وهذا يعني أننا نحتفظ بمعلومات ملفك الشخصي طالما أنك تحتفظ بحساب. نحن نحتفظ بمعلومات المعاملات مثل الرحلات والمدفوعات لمدة سبع سنوات على الأقل لضمان قدرتنا على أداء وظائف العمل المشروعة، مثل المحاسبة عن الالتزامات الضريبية. إذا طلبت حذف الحساب، فسنقوم بحذف معلوماتك على النحو المبين في قسم "حذف حسابك" أدناه. نحن نتخذ التدابير المعقولة والمناسبة المصممة لحماية معلوماتك الشخصية. ولكن لا توجد إجراءات أمنية يمكن أن تكون فعالة بنسبة 100%، ولا يمكننا ضمان أمان معلوماتك، بما في ذلك ضد التدخلات غير المصرح بها أو الأعمال التي تقوم بها أطراف ثالثة.</p>
                
                <p>حقوقك وخياراتك فيما يتعلق ببياناتك</p>
                
                <p>توفر لك Mi Softwares طرقًا للوصول إلى معلوماتك الشخصية وحذفها بالإضافة إلى ممارسة حقوق البيانات الأخرى التي تمنحك تحكمًا معينًا في معلوماتك الشخصية.</p>
                
                <p>أ. كافة المستخدمين</p>
                
                <p>اشتراكات البريد الإلكتروني. يمكنك دائمًا إلغاء الاشتراك في رسائل البريد الإلكتروني التجارية أو الترويجية الخاصة بنا عن طريق النقر فوق إلغاء الاشتراك في تلك الرسائل. سنستمر في إرسال رسائل البريد الإلكتروني المتعلقة بالمعاملات والعلائقية إليك حول استخدامك لمنصة Mi Softwares.<br />
                <br />
                <strong>الرسائل النصية.</strong> يمكنك إلغاء الاشتراك في تلقي النصوص التجارية أو الترويجية. يمكنك أيضًا إلغاء الاشتراك في تلقي جميع النصوص من Mi Softwares (بما في ذلك رسائل المعاملات أو الرسائل العلائقية. لاحظ أن إلغاء الاشتراك في تلقي جميع النصوص قد يؤثر على استخدامك لمنصة Mi Softwares. يمكن للسائقين أيضًا إلغاء الاشتراك في الرسائل الخاصة بالسائق عن طريق إرسال رسالة نصية STOP ردًا على رسالة نصية قصيرة خاصة بالسائق لإعادة تمكين الرسائل النصية، يمكنك إرسال رسالة نصية بعنوان "START" ردًا على رسالة تأكيد إلغاء الاشتراك.<br />
                <br />
                <strong>الإشعارات الفورية.</strong> يمكنك إلغاء الاشتراك في تلقي الإشعارات الفورية من خلال إعدادات جهازك. يرجى ملاحظة أن إلغاء الاشتراك في تلقي الإشعارات قد يؤثر على استخدامك لمنصة Mi Softwares (مثل تلقي إشعار بوصول رحلتك).<br />
                <br />
                <strong>معلومات الملف الشخصي</strong>. يمكنك مراجعة وتعديل بعض معلومات الحساب التي اخترت إضافتها إلى ملفك الشخصي عن طريق تسجيل الدخول إلى إعدادات حسابك وملفك الشخصي.<br />
                <br />
                <strong>معلومات الموقع.</strong> يمكنك منع جهازك من مشاركة معلومات الموقع من خلال إعدادات النظام بجهازك. ولكن إذا قمت بذلك، فقد يؤثر ذلك على قدرة Mi Softwares على توفير مجموعة كاملة من الميزات والخدمات لك.<br />
                <br />
                <strong>تتبع ملفات تعريف الارتباط.</strong> يمكنك تعديل إعدادات ملفات تعريف الارتباط الخاصة بك على متصفحك، ولكن إذا قمت بحذف ملفات تعريف الارتباط الخاصة بنا أو اخترت عدم قبولها، فقد تفوتك ميزات معينة في منصة Mi Softwares.<br />
                <br />
                <strong>عدم التتبع.</strong> قد يقدم لك متصفحك خيار "عدم التتبع"، والذي يسمح لك بإرسال إشارة إلى مشغلي مواقع الويب وتطبيقات الويب والخدمات التي لا تريد منهم أن يتتبعوا أنشطتك عبر الإنترنت. لا يدعم نظام Mi Softwares Platform حاليًا طلبات عدم التتبع في الوقت الحالي.<br />
                <br />
                <strong>حذف حسابك.</strong> إذا كنت ترغب في حذف حساب Mi Softwares الخاص بك، يرجى زيارة صفحة الخصوصية الرئيسية لدينا. في بعض الحالات، لن نتمكن من حذف حسابك، كما هو الحال في حالة وجود مشكلة في حسابك تتعلق بالثقة أو الأمان أو الاحتيال. عندما نحذف حسابك، قد نحتفظ بمعلومات معينة لأغراض تجارية مشروعة أو للامتثال لالتزامات قانونية أو تنظيمية. على سبيل المثال، قد نحتفظ بمعلوماتك لحل مطالبات التأمين المفتوحة، أو قد نكون ملزمين بالاحتفاظ بمعلوماتك كجزء من مطالبة قانونية مفتوحة. عندما نحتفظ بمثل هذه البيانات، فإننا نفعل ذلك بطرق مصممة لمنع استخدامها لأغراض أخرى.<br />
                <br />
                <strong>الحق في المعرفة.</strong> لديك الحق في معرفة ومعرفة البيانات التي جمعناها، بما في ذلك:</p>
                
                <ul>
                <li>فئات المعلومات الشخصية التي جمعناها عنك؛</li>
                <li>فئات المصادر التي يتم جمع المعلومات الشخصية منها؛</li>
                <li>الغرض التجاري أو التجاري لجمع معلوماتك الشخصية؛</li>
                <li>فئات الأطراف الثالثة التي شاركنا معها معلوماتك الشخصية؛ و</لي>
                <li>الأجزاء المحددة من المعلومات الشخصية التي جمعناها عنك.</li>
                </ul>
                
                <p><strong>الحق في الحذف.</strong> لديك الحق في أن تطلب منا حذف المعلومات الشخصية التي جمعناها منك (وتوجيه مقدمي الخدمة لدينا للقيام بالشيء نفسه). ومع ذلك، هناك عدد من الاستثناءات التي تشمل، على سبيل المثال لا الحصر، عندما تكون المعلومات ضرورية لنا أو لطرف ثالث للقيام بأي مما يلي:</p>
                
                <ul>
                <li>أكمل معاملتك؛</li>
                <li>نقدم لك سلعة أو خدمة؛</li>
                <li>إبرام عقد بيننا وبينك؛</li>
                <li>حماية أمنك ومحاكمة المسؤولين عن انتهاكه؛</li>
                <li>إصلاح نظامنا في حالة وجود خطأ؛</li>
                <li>حماية حقوق حرية التعبير لك أو للمستخدمين الآخرين؛</li>
                <li>المشاركة في الأبحاث العلمية أو التاريخية أو الإحصائية العامة أو الخاضعة لمراجعة النظراء لتحقيق المصلحة العامة والتي تلتزم بجميع قوانين الأخلاق والخصوصية الأخرى المعمول بها.</li>
                <li>الامتثال لالتزام قانوني؛ أو</li>
                <li>قم باستخدامات داخلية وقانونية أخرى للمعلومات التي تتوافق مع السياق الذي قدمتها فيه.</li>
                </ul>
                
                <p><strong>حقوق أخرى.</strong> يمكنك طلب معلومات معينة حول الكشف عن المعلومات الشخصية لأطراف ثالثة لأغراض التسويق المباشر الخاصة بهم خلال السنة التقويمية السابقة. هذا الطلب مجاني ويمكن تقديمه مرة واحدة في السنة. لديك أيضًا الحق في عدم التعرض للتمييز بسبب ممارسة أي من الحقوق المذكورة أعلاه.<br />
                <br />
                <strong>موقع الويب:</strong> يمكنك زيارة صفحة الخصوصية الرئيسية لدينا لمصادقة الحقوق وممارستها عبر موقعنا على الويب.<br />
                <br />
                <strong>أرسل نموذج الويب عبر البريد الإلكتروني:</strong> يمكنك مراسلتنا لممارسة الحقوق. للرد على بعض الحقوق، سنحتاج إلى التحقق من طلبك إما عن طريق مطالبتك بتسجيل الدخول والمصادقة على حسابك أو التحقق من هويتك من خلال تقديم معلومات عنك أو عن حسابك. يمكن للوكلاء المعتمدين تقديم طلب نيابةً عنك إذا منحتهم توكيلًا قانونيًا أو تم تزويدنا بدليل على الإذن الموقع والتحقق من هويتك وتأكيد أنك قدمت الإذن للوكيل بإرسال الطلب. توقيت الاستجابة والتنسيق. ونحن نهدف إلى الرد على طلب المستهلك للوصول أو الحذف خلال 45 يومًا من تلقي هذا الطلب. إذا كنا بحاجة إلى المزيد من الوقت، فسنبلغك بالسبب وفترة التمديد كتابيًا.</p>
                
                <p>بيانات الأطفال</p>
                
                <p>برامج Mi ليست موجهة للأطفال، ونحن لا نجمع معلومات شخصية عن عمد من الأطفال تحت سن 13 عامًا. إذا اكتشفنا أن طفلًا أقل من 13 عامًا قد قدم لنا معلومات شخصية، فسنتخذ الخطوات اللازمة لحذف تلك المعلومات معلومة. إذا كنت تعتقد أن طفلًا يقل عمره عن 13 عامًا قد قدم لنا معلومات شخصية، فيرجى الاتصال بنا</p>
                
                <p>روابط لمواقع الطرف الثالث</p>
                
                <p>قد تحتوي منصة Mi Softwares Platform على روابط لمواقع ويب تابعة لجهات خارجية. قد يكون لهذه المواقع سياسات خصوصية تختلف عن سياساتنا. نحن لسنا مسؤولين عن تلك المواقع، ونوصيك بمراجعة سياساتها. يرجى الاتصال بهذه المواقع مباشرة إذا كانت لديك أية أسئلة حول سياسات الخصوصية الخاصة بها.</p>
                
                <p>التغييرات على سياسة الخصوصية هذه</p>
                
                <p>يجوز لنا تحديث هذه السياسة من وقت لآخر مع تغير نظام Mi Softwares Platform وتطور قانون الخصوصية. إذا قمنا بتحديثه، فسنقوم بذلك عبر الإنترنت، وإذا أجرينا تغييرات جوهرية، فسنخبرك بذلك من خلال منصة Mi Softwares Platform أو عن طريق وسيلة اتصال أخرى مثل البريد الإلكتروني. عندما تستخدم Mi Softwares، فإنك توافق على أحدث شروط هذه السياسة.</p>
                
                <p>اتصل بنا</p>
                
                <p>إذا كانت لديك أية أسئلة أو مخاوف بشأن خصوصيتك أو أي شيء في هذه السياسة، بما في ذلك إذا كنت بحاجة إلى الوصول إلى هذه السياسة بتنسيق بديل، فنحن نشجعك على الاتصال بنا.</p>',
            'terms_title' => 'الشروط والأحكام',
            'terms' => '<h2><strong>الشروط والأحكام</strong></h2>

                <p>اتفاقية ترخيص المستخدم النهائي</p>

                <p>آخر تحديث في 16 مايو 2021</p>

                <p>تم ترخيص Mi Softwares,LLC لك (المستخدم النهائي) من شركة Mi Softwares, LLC، ومقرها في 6255 Towncenter Drive Ste 819، Clemmons، North Carolina 27012، الولايات المتحدة (المشار إليها فيما يلي: المرخص)، للاستخدام فقط بموجب الشروط. من اتفاقية الترخيص هذه.<br />
                <br />
                عن طريق تنزيل التطبيق من Apple AppStore وGoogle Play، وأي تحديث له (كما تسمح به اتفاقية الترخيص هذه)، فإنك تشير إلى أنك توافق على الالتزام بجميع شروط وأحكام اتفاقية الترخيص هذه، وأنك تقبل هذا اتفاقية الترخيص.<br />
                <br />
                يقر أطراف اتفاقية الترخيص هذه بأن Apple و/أو Google Play ليسوا طرفًا في اتفاقية الترخيص هذه وغير ملزمين بأي أحكام أو التزامات فيما يتعلق بالتطبيق، مثل الضمان والمسؤولية والصيانة والدعم الخاص به. تتحمل شركة Mi Softwares, LLC، وليس Apple أو Google Play، المسؤولية الوحيدة عن التطبيق المرخص ومحتواه.<br />
                <br />
                لا يجوز أن تنص اتفاقية الترخيص هذه على قواعد الاستخدام للتطبيق التي تتعارض مع أحدث شروط خدمة متجر التطبيقات. تقر شركة Mi Softwares, LLC بأنها أتيحت لها الفرصة لمراجعة الشروط المذكورة وأن اتفاقية الترخيص هذه لا تتعارض معها.<br />
                <br />
                جميع الحقوق غير الممنوحة لك صراحةً محفوظة.</p>

                <p>1. التطبيق</p>

                <p>Mi Softwares (المشار إليها فيما يلي: التطبيق) هي جزء من البرنامج عبارة عن منصة Rideshare - ومخصصة لأجهزة Apple وAndroid المحمولة. يتم استخدامه لتوصيل الركاب بالسائقين للوصول إلى النقطة A إلى B بضغطة زر واحدة.<br />
                <br />
                لم يتم تصميم التطبيق ليتوافق مع اللوائح الخاصة بالصناعة (قانون قابلية نقل التأمين الصحي والمساءلة (HIPAA)، والقانون الفيدرالي لإدارة أمن المعلومات (FISMA)، وما إلى ذلك)، لذلك إذا كانت تفاعلاتك ستخضع لمثل هذه القوانين، فلا يجوز لك استخدام هذا التطبيق. لا يجوز لك استخدام التطبيق بطريقة تنتهك قانون Gramm-Leach-Bliley (GLBA).</p>

                <p>2. نطاق الترخيص</p>

                <p>2.1 يتم منحك ترخيصًا غير قابل للتحويل وغير حصري وغير قابل للترخيص من الباطن لتثبيت التطبيق المرخص واستخدامه على أي منتجات تحمل علامة Apple التجارية أو منتجات Google التي تمتلكها أو تتحكم فيها (المستخدم النهائي) ووفقًا لما يسمح به قواعد الاستخدام المنصوص عليها في هذا القسم وشروط خدمة App Store، باستثناء أنه يمكن الوصول إلى هذا التطبيق المرخص واستخدامه بواسطة حسابات أخرى مرتبطة بك (المستخدم النهائي، المشتري) عبر المشاركة العائلية أو المجلد. الشراء.<br />
                <br />
                2.2 سيحكم هذا الترخيص أيضًا أي تحديثات للتطبيق المقدم من قبل المرخص والتي تحل محل التطبيق الأول و/أو تصلحه و/أو تكمله، ما لم يتم توفير ترخيص منفصل لمثل هذا التحديث وفي هذه الحالة ستسري شروط هذا الترخيص الجديد.<br />
                <br />
                2.3 لا يجوز لك مشاركة التطبيق أو إتاحته لأطراف ثالثة (ما لم يكن بالدرجة التي تسمح بها شروط وأحكام Apple، وبموافقة كتابية مسبقة من Mi Softwares, LLC)، أو بيع التطبيق أو تأجيره أو إقراضه أو تأجيره أو إعادة توزيعه بطريقة أخرى. <br />
                <br />
                2.4 لا يجوز لك إجراء هندسة عكسية، أو ترجمة، أو تفكيك، أو دمج، أو إلغاء ترجمة، أو دمج، أو إزالة، أو تعديل، أو دمج، أو إنشاء أعمال مشتقة أو تحديثات، أو تكييف، أو محاولة اشتقاق الكود المصدري للتطبيق، أو أي جزء منه (ما عدا مع موافقة كتابية مسبقة من شركة Mi Softwares, LLC).<br />
                <br />
                2.5 لا يجوز لك نسخ (باستثناء الحالات التي يسمح فيها هذا الترخيص وقواعد الاستخدام صراحةً) أو تغيير التطبيق أو أجزاء منه. لا يجوز لك إنشاء نسخ وتخزينها إلا على الأجهزة التي تمتلكها أو تتحكم فيها للاحتفاظ بنسخة احتياطية بموجب شروط هذا الترخيص وشروط خدمة App Store وأي شروط وأحكام أخرى تنطبق على الجهاز أو البرنامج المستخدم. لا يجوز لك إزالة أي إشعارات للملكية الفكرية. أنت تقر أنه لا يجوز لأي أطراف ثالثة غير مصرح بها الوصول إلى هذه النسخ في أي وقت. <br />
                <br />
                2.6 انتهاكات الالتزامات المذكورة أعلاه ، وكذلك محاولة هذا الانتهاك ، قد تخضع للملاحقة القضائية والأضرار. <br />
                <br />
                2.7 يحتفظ المرخص بالحق في تعديل شروط وأحكام الترخيص. <BR />
                <br />
                2.8 لا ينبغي تفسير أي شيء في هذا الترخيص لتقييد شروط الطرف الثالث. عند استخدام التطبيق ، يجب عليك التأكد من امتثالك لشروط وأحكام الطرف الثالث المعمول بها. </p>

                <ص>3. المتطلبات الفنية </p>

                <p> 3.1 يحاول Licensor الحفاظ على تحديث التطبيق بحيث يتوافق مع الإصدارات المعدلة/الجديدة من البرامج الثابتة والأجهزة الجديدة. لا تُمنح حقوقًا في المطالبة بمثل هذا التحديث. <br />
                <br />
                3.2 أنت تقر بأنه تقع على عاتقك مسؤولية تأكيد أن جهاز المستخدم النهائي للتطبيق الذي تنوي استخدام التطبيق يفي بالمواصفات الفنية المذكورة أعلاه. <br />
                <br />
                3.3 يحتفظ المرخص بالحق في تعديل المواصفات الفنية كما هو مناسب في أي وقت. </p>

                <p> 4. الصيانة والدعم </p>

                <p> 4.1 يكون المرخص مسؤولاً فقط عن توفير أي خدمات صيانة ودعم لهذا التطبيق المرخص. يمكنك الوصول إلى المرخص على عنوان البريد الإلكتروني المدرج في متجر التطبيقات أو نظرة عامة على Google Play لهذا التطبيق المرخص. <BR />
                <br />
                4.2 MI Softwares ، LLC والمستخدم النهائي يعترفان بأن Apple و OR Play ليس عليهم التزام على الإطلاق بتقديم أي خدمات صيانة ودعم فيما يتعلق بالتطبيق المرخص. </p>

                <p> 5. استخدام البيانات </p>

                ] www.mi softwares.us/privacy. </p>

                <p> 6. المساهمات التي أنشأها المستخدم </p>

                <p> يجوز للتطبيق أن يدعوك للدردشة أو المساهمة في المدونات أو لوحات الرسائل والمنتديات عبر الإنترنت والوظائف الأخرى ، وقد يوفر لك الفرصة لإنشاء أو إرسال أو عرض أو إرسال أو تنفيذ وأداء أو نشر أو توزيع المحتوى والمواد أو بثه لنا أو في التطبيق ، بما في ذلك على سبيل المثال لا الحصر ، النص أو الكتابات أو الفيديو أو الصوت أو الصور الفوتوغرافية أو الرسومات أو التعليقات أو الاقتراحات أو المعلومات الشخصية أو مواد أخرى (مجتمعة ، "مساهمات"). يمكن عرض المساهمات من قبل المستخدمين الآخرين للتطبيق ومن خلال مواقع أو تطبيقات الطرف الثالث. على هذا النحو ، قد يتم التعامل مع أي مساهمات تنقلها على أنها غير سرية وغير مقدمة. عندما تقوم بإنشاء أو إتاحة أي مساهمات ، فأنت تمثل وتتعاون: <br />
                <br />
                1. إن إنشاء مساهماتك أو توزيعها أو نقلها أو عرضها العام أو الأداء ، والوصول إلى أو تنزيل أو نسخ مساهماتك ، لن تنتهك حقوق الملكية ، بما في ذلك على سبيل المثال أو الحقوق الأخلاقية لأي طرف ثالث. <br />
                <br />
                2. أنت منشئ ومالك التراخيص والحقوق والموافقات والإصدارات والأذونات اللازمة لاستخدامنا وتفويضها والتطبيق ومستخدمي التطبيق الآخرين لاستخدام مساهماتك بأي طريقة تفكر فيها التطبيق وشروط الاستخدام هذه. <br />
                <br />
                3. لديك موافقة كتابية وإصدار و/أو إذن من كل فرد يمكن تحديده في مساهماتك لاستخدام الاسم أو الشبه أو كل شخص فردي يمكن تحديده لتمكين إدراج واستخدام مساهماتك بأي طريقة تم التفكير فيها حسب التطبيق وشروط الاستخدام هذه. <br />
                <br />
                4. مساهماتك ليست خاطئة أو غير دقيقة أو مضللة. <br />
                <br />
                5. مساهماتك ليست إعلانات غير مرغوب فيها أو غير مصرح بها ، أو مواد ترويجية ، أو مخططات هرمية ، أو رسائل السلسلة ، أو البريد العشوائي ، أو المراسلات الجماعية ، أو غيرها من أشكال التماس. <br />
                <br />
                6. مساهماتك ليست فاحشة أو بذيئة أو واسعة أو قذرة أو عنيفة أو مضايقة أو تشهيرية أو مخططة أو غير مرغوب فيها (كما هو محدد من قبلنا). <br />
                <br />
                7. مساهماتك لا تسخر أو وهمية أو تخويف أو تخويف أو إساءة استخدام أي شخص. <br />
                <br />
                8. لا يتم استخدام مساهماتك لمضايقة أو تهديد (بالمعنى القانوني لتلك المصطلحات) أي شخص آخر وتعزيز العنف ضد شخص معين أو فئة من الناس. <br />
                <br />
                9. مساهماتك لا تنتهك أي قانون أو لائحة أو قاعدة معمول بها. <br />
                <br />
                10. مساهماتك لا تنتهك حقوق الخصوصية أو الدعاية لأي طرف ثالث. <br />
                <br />
                11. لا تحتوي مساهماتك على أي مادة تلتمس المعلومات الشخصية من أي شخص دون سن 18 أو يستغل الناس دون سن 18 عامًا بطريقة جنسية أو عنيفة. <BR />
                <br />
                12. لا تنتهك مساهماتك أي قانون معمول به فيما يتعلق بالمواد الإباحية عن الأطفال ، أو يهدف إلى حماية صحة أو رفاهية القاصرين. <br />
                <br />
                13. لا تتضمن مساهماتك أي تعليقات هجومية مرتبطة بالعرق أو الأصل القومي أو الجنس أو التفضيل الجنسي أو المعوق الجسدي. <br />
                <br />
                14. لا تنتهك مساهماتك أو ترتبط بالمواد التي تنتهك أي حكم من شروط الاستخدام هذه أو أي قانون أو لائحة معمول بها. <br />
                <br />
                أي استخدام للتطبيق في انتهاك ما سبق ينتهك شروط الاستخدام هذه وقد يؤدي ، من بين أشياء أخرى ، إنهاء أو تعليق حقوقك في استخدام التطبيق. </p>

                <p> 7. ترخيص المساهمة </p>

                <p> من خلال نشر مساهماتك في أي جزء من التطبيق أو تقديم مساهمات في متناول التطبيق عن طريق ربط حسابك من التطبيق إلى أي من حسابات الشبكات الاجتماعية الخاصة بك ، فإنك تمنحها تلقائيًا ، وتمثل وتبرير أن لديك الحق في ذلك منح لنا ، لنا غير مقيد ، غير محدود ، لا رجعة فيه ، دائم ، غير حصري ، قابل للتحويل ، خالي من الملوك ، مدفوعين بالكامل ، في جميع أنحاء العالم ، وترخيص لاستضافة النسخ ، إعادة إنتاج ، الكشف ، بيع ، إعادة بيع ، نشر ، نشر ، طاقم عمل واسع ، رعيد ، أرشيف ، متجر ، ذاكرة التخزين المؤقت ، عرض علنيًا ، إعادة تنسيق ، ترجمة ، نقل ، مقتطفات (كليًا أو جزئيًا) ، وتوزيع هذه المساهمات (بما في ذلك ، على سبيل المثال لا الحصر ، صورتك وصوتك) لأي غرض ، إعلانات تجارية ، أو غير ذلك ، وإعداد أعمال مشتقة أو دمجها في أعمال أخرى ، مثل المساهمات ، ومنح وتأمينات Sublicments السابقة. قد يحدث الاستخدام والتوزيع في أي تنسيقات وسائط ومن خلال أي قنوات إعلامية. <br />
                <br />
                سيتم تطبيق هذا الترخيص على أي نموذج أو وسائل الإعلام أو التكنولوجيا المعروفة الآن أو يتم تطويره فيما بعد ، ويتضمن استخدامنا لاسمك واسم الشركة واسم الامتياز ، على النحو المطلوب ، وأي من العلامات التجارية ، علامات الخدمة ، الأسماء التجارية ، الشعارات ، والصور الشخصية والتجارية التي تقدمها. تتنازل عن جميع الحقوق الأخلاقية في مساهماتك ، وتضمن أن الحقوق الأخلاقية لم يتم تأكيدها في مساهماتك. <br />
                <br />
                نحن لا نؤكد أي ملكية على مساهماتك. يمكنك الاحتفاظ بملكية كاملة لجميع مساهماتك وأي حقوق الملكية الفكرية أو غيرها من حقوق الملكية المرتبطة بمساهماتك. نحن لسنا مسؤولين عن أي بيانات أو تمثيلات في مساهماتك المقدمة في أي مجال في التطبيق. أنت وحدك مسؤول عن مساهماتك في الطلب وتوافق صراحة على تبرئةنا من أي وكل مسؤولية وامتناع عن أي إجراء قانوني ضدنا فيما يتعلق بمساهماتك. <br />
                <br />
                لدينا الحق ، وفقًا لتقديرنا المطلق والمطلق ، (1) لتحرير أي مساهمات أو تنقيحها أو تغييرها بطريقة أخرى ؛ (2) إعادة تصنيف أي مساهمات لوضعها في مواقع أكثر ملاءمة في التطبيق ؛ و (3) لشراء أو حذف أي مساهمات في أي وقت ولأي سبب ، دون إشعار. ليس لدينا أي التزام بمراقبة مساهماتك. </p>

                <p> 8. المسؤولية </p>

                <p> 8.1 مسؤولية المرخص في حالة انتهاك الالتزامات والضرر يقتصر على القصد والإهمال الجسيم. فقط في حالة خرق الواجبات التعاقدية الأساسية (الالتزامات الأساسية) ، يجب أن يكون المرخص مسؤولاً أيضًا في حالة الإهمال الطفيف. في أي حال ، تقتصر المسؤولية على الأضرار النموذجية المنظورة. لا ينطبق القيد المذكور أعلاه على الإصابات في الحياة أو الأطراف أو الصحة. <br />
                <br />
                8.2 لا يتحمل المرخص أي مساءلة أو مسؤولية عن أي أضرار تسبب بسبب خرق الواجبات وفقًا للمادة 2 من هذه الاتفاقية. لتجنب فقدان البيانات ، يتعين عليك الاستفادة من وظائف النسخ الاحتياطي للتطبيق إلى الحد الذي يسمح به شروط الاستخدام الجهات الخارجية المعمول بها. أنت تدرك أنه في حالة التعديلات أو التلاعب بالتطبيق ، لن تتمكن من الوصول إلى التطبيق المرخص. </p>

                <p> 9. الضمان </p>

                <p> 9.1 يستدعي المرخصين أن يكون التطبيق خاليًا من برامج التجسس أو خيول طروادة أو فيروسات أو أي برامج ضارة أخرى في وقت التنزيل. يستدعي المرخصين أن التطبيق يعمل كما هو موضح في وثائق المستخدم. <br />
                <br />
                9.2 لا يتم توفير أي ضمان للتطبيق غير القابل للتنفيذ على الجهاز ، والذي تم تعديله بشكل غير مصرح به ، أو معالجته بشكل غير لائق أو معلما ، أو مجتمعة أو مثبتة بأجهزة أو برامج غير لائقة ، وتستخدم مع إكسسوارات غير لائقة ، بغض النظر عما إذا كانت بنفسك أو بأطراف ثالثة ، بحلول أطراف ثالثة ، أو إذا كان هناك أي أسباب أخرى خارج Mi Softwares ، مجال تأثير LLC الذي يؤثر على قابلية التطبيق. <br />
                <br />
                9.3 ، يُطلب منك فحص التطبيق فورًا بعد تثبيته وإخطار Mi Softwares ، LLC حول المشكلات التي تم اكتشافها دون تأخير عن طريق البريد الإلكتروني المقدم في مطالبات المنتج. سيتم أخذ تقرير العيوب في الاعتبار وسيتم التحقيق بشكل إضافي إذا تم إرساله بالبريد خلال فترة تسعين (90) يومًا بعد الاكتشاف. <br />
                <br />
                9.4 إذا أكدنا أن التطبيق معيب ، تحتفظ MI Softwares ، LLC باختيار لعلاج الموقف إما عن طريق حل العيب أو التسليم البديل. <BR />
                <br />
                9.5 في حالة وجود أي فشل في التطبيق في التوافق مع أي ضمان قابل للتطبيق ، يمكنك إخطار تشغيل متجر التطبيقات ، وسيتم استرداد سعر شراء التطبيق الخاص بك. إلى أقصى حد يسمح به القانون المعمول به ، لن يكون لدى مشغل متجر التطبيقات أي التزام ضمان آخر على الإطلاق فيما يتعلق بالتطبيق ، وأي خسائر ومطالبات أخرى وأضرار وتخصيصات ونفقات وتكاليف تعزى إلى أي إهمال على الالتزام بأي شيء الضمان. <br />
                <br />
                9.6 إذا كان المستخدم رائد أعمال ، فإن أي مطالبة تعتمد على الأخطاء تنتهي بعد فترة قانونية من التقادم التي تصل إلى اثني عشر (12) شهرًا بعد إتاحة الطلب للمستخدم. تنطبق الفترات القانونية للتقييد المقدم بموجب القانون على المستخدمين المستهلكين. </p>

                <p> 10. مطالبات المنتج </p>

                يعترف <P> Mi Softwares ، LLC والمستخدم النهائي بأن Mi Softwares ، LLC ، وليس Apple ، مسؤولون عن معالجة أي مطالبات للمستخدم النهائي أو أي طرف ثالث يتعلق بالتطبيق المرخص أو حيازة المستخدم النهائي و /أو استخدام هذا التطبيق المرخص ، بما في ذلك ، على سبيل المثال لا الحصر: <br />
                <br />
                (ط) مطالبات مسؤولية المنتج ؛ <br />
                <br />
                (2) أي مطالبة بأن الطلب المرخص يفشل في الامتثال لأي متطلبات قانونية أو تنظيمية معمول بها ؛ و <br />
                <br />
                (3) المطالبات الناشئة تحت حماية المستهلك أو الخصوصية أو التشريعات المماثلة ، بما في ذلك فيما يتعلق باستخدام طلبك المرخص. </p>

                <p> 11. الامتثال القانوني </p>

                <p> أنت تمثل وتتعهد أنك لا تقع في بلد يخضع للحصار الحكومي الأمريكي ، أو التي تم تعيينها من قبل الحكومة الأمريكية كدولة "إرهابية تدعم" ؛ وأنك غير مدرج في أي قائمة حكومية أمريكية من الأطراف المحظورة أو المقيدة. </p>

                <p> 12. معلومات الاتصال </p>

                <p> للاستفسارات العامة أو الشكاوى أو الأسئلة أو المطالبات المتعلقة بالتطبيق المرخص ، يرجى الاتصال: <br />
                <br />
                <strong> MI Postwares ، LLC <BR />
                6255 Towncenter Drive Ste 819 <Br />
                Clemmons ، NC 27012 <BR />
                الولايات المتحدة <br />
                دعم@MI Softwares.us </strong> </p>

                <p> 13. إنهاء </p>

                <p> يكون الترخيص صالحًا حتى يتم إنهاءه بواسطة Mi Softwares ، LLC أو من قبلك. ستنتهي حقوقك بموجب هذا الترخيص تلقائيًا ودون إشعار من MI Softwares ، LLC إذا فشلت في الالتزام بأي مصطلح (ق) من هذا الترخيص. عند إنهاء الترخيص ، يجب عليك إيقاف كل استخدام التطبيق ، وتدمير جميع النسخ ، كاملة أو جزئية ، للتطبيق. </p>

                <p> 14. شروط الاتفاقيات والمستفيد من الطرف الثالث </p>

                <p> MI Softwares ، LLC تمثل وتستدعي أن MI Softwares ، LLC ستقوم بالامتثال لشروط الاتفاقية المعمول بها عند استخدام التطبيق المرخص. <BR />
                <br />
                وفقًا للمادة 9 من "التعليمات الخاصة بشروط الحد الأدنى لاتفاقية ترخيص المستخدم النهائي للمطور" ، يجب أن تكون شركة Apple و Apple مستفيدين من طرف ثالث من اتفاقية ترخيص المستخدم النهائي هذه-وبناءً على قبولك لشروط وأحكام هذا الترخيص اتفاق ، سيكون للآبل الحق (وسيُعتبر أنه قد قبل الحق) في تطبيق اتفاقية ترخيص المستخدم النهائي ضدك كمستفيد من طرف ثالث. </p>

                <p> 15. حقوق الملكية الفكرية </p>

                <p> Mi Softwares ، LLC والمستخدم النهائي يقران أنه في حالة ادعاء أي طرف ثالث أن الطلب المرخص أو حيازة المستخدم النهائي واستخدام هذا الطلب المرخص ينتهك حقوق الملكية الفكرية للطرف الثالث ، MI ستكون البرامج ، LLC ، وليس Apple ، مسؤولة فقط عن التحقيق أو الدفاع والتسوية والتفريغ أو أي مطالبات انتهاك للملكية الفكرية. </p>

                <p> 16. القانون المعمول به </p>

                <p> تخضع اتفاقية الترخيص هذه لقوانين ولاية كارولينا الشمالية باستثناء تعارضها في قواعد القانون. </p>

                <p> 17. متنوعة </p>

                <p> 17.1 إذا كانت أي من شروط هذه الاتفاقية يجب أن تكون أو تصبح غير صالحة ، فلن تتأثر صحة الأحكام المتبقية. سيتم استبدال المصطلحات غير الصالحة بأخرى صالحة تم صياغتها بطريقة ستحقق الغرض الأساسي. <br />
                <br />
                17.2 اتفاقيات الجانبية والتغييرات والتعديلات صالحة فقط إذا تم وضعها في الكتابة. لا يمكن التنازل عن البند السابق إلا في الكتابة. </p> ',
            'compliance_title' => 'الامتثال',
            'compliance' => '<H3> <strong> فرصة التوظيف المتساوية وسياسة عدم التمييز </strong> </h3>

                <h3> أنا. نظرة عامة ونطاق </h3>

                <p> Mi Softwares ، LLC من 6255 Towncenter Drive Ste 819 ، Clemmons ، North Carolina 27012 ، أنشأت سياسة غير تمييزية وتكافؤ فرص العمل ("EEO"). تنطبق سياسة EEO هذه على جميع جوانب العلاقة بين Mi Postwares ، LLC وموظفيها ، بما في ذلك ، على سبيل المثال لا الحصر ، التوظيف ، التوظيف ، الإعلانات للتوظيف ، التوظيف والاطلاق ، التعويض ، المهمة ، تصنيف الموظفين ، الإنهاء ، الترقية ، الترقيات ، النقل ، التدريب ، ظروف العمل ، الأجور وإدارة الرواتب ، ومزايا الموظفين وتطبيق السياسات. تنطبق هذه السياسات على المقاولين المستقلين ، والموظفين المؤقتين ، وجميع الموظفين العاملين في المبنى ، وأي أشخاص أو شركات أخرى تقوم بأعمال تجارية من أجل أو مع MI Softwares ، LLC. أي مستخدم وجد أنه انتهك هذا الحظر سوف يفقد الوصول إلى منصة MI Softwares ، LLC. قد تتطلب القوانين المعمول بها في بعض الولايات القضائية و/أو تسمح بتوفير الخدمات من خلال وصالح فئة معينة من الأشخاص. في مثل هذه السلطات القضائية ، يتم السماح بالخدمات المقدمة في الامتثال لهذه القوانين والشروط المعمول بها ذات الصلة بموجب هذه السياسة. </p>

                <H3> II. السياسات </h3>

                <p>1. التمييز. </p>

                يجب ألا تتسامح <p> Mi Softwares ، LLC ، تحت أي ظرف من الظروف ، دون استثناء ، أي شكل من أشكال التمييز على أساس العرق ، العقيدة ، الدين ، اللون ، العمر ، العجز ، الحمل ، الحالة الزواجية ، الحالة الوالدية ، الميل الجنسي ، التعبير الجنساني ، الهوية الجنسية ، الوضع المخضرم ، الوضع العسكري ، وضع ضحية العنف المنزلي ، الأصل القومي ، الانتماء السياسي ، الجنس ، الخصائص الوراثية المؤهلة ، أو الموقع الجغرافي وأي حالة أخرى محمية بموجب القانون. هذه القائمة ليست شاملة. بالنسبة للأشخاص المؤهلين ذوي الإعاقة ، ستقوم MI Softwares ، LLC ببذل قصارى جهدها لتوفير أماكن إقامة معقولة في مكان العمل التي تمتثل للقوانين المعمول بها. </p>

                <p> التمييز في توفير خدمات النقل محظور بشكل صارم </p>

                <p> يُطلب من السائقين والموظفين المرتبطة بمعرفة حظر عدم التمييز. لن تتسامح Mi Softwares ، LLC مع أماكن الإقامة العامة ، والتي تشمل خدمات سيارات الأجرة الممارسة التمييزية غير القانونية لإنكار ، بشكل مباشر أو غير مباشر ، أي شخص يتمتع بالسلع والخدمات والمرافق والامتيازات والامتيازات بشكل مباشر أو غير مباشر. من أماكن الإقامة العامة (بما في ذلك خدمات سيارات الأجرة) كليًا أو جزئيًا لسبب تمييزي قائم على مكان الإقامة أو الأعمال التجارية. </p>

                <p> ممنوع السلوك التمييزي: </p>

                <p> Mi Softwares ، LLC تدرك أن السائقين المرتبطين يجب ألا يميزوا أبدًا ضد بعض العملاء من خلال عدم التقاطهم ، وليس أخذهم إلى حيث يرغبون في الذهاب أو عن طريق معاملتهم باحترام أقل بناءً على الخصائص المحمية أو السمات المذكورة أعلاه. أمثلة محددة للسلوك التمييزي ، تشمل ما يلي: <br />
                <br />
                عدم التقاط راكب على أساس أي خاصية أو سمة محمية ، بما في ذلك عدم التقاط راكب مع حيوان خدمة · يطلب من راكب الخروج من سيارة أجرة على أساس خاصية محمية أو سمة · استخدام لغة مهينة أو مضايقة على أساس خاصية محمية أو سمة · رفض التقاط في منطقة جغرافية معينة. </p>

                <p> التمييز الجغرافي: </p>

                لا تتسامح <p> Mi Softwares ، LLC إلى التمييز الجغرافي ويدرك مدى أهمية نقل العميل إلى الوجهة المطلوبة دون التمييز ضد ذلك العميل بناءً على المكان الذي يرغب فيه في الذهاب. جميع السائقين والموظفين والمديرين وأصحاب المصلحة والوكلاء في MI Softwares ، LLC سوف يتوافق مع سياسات مكافحة التمييز هذه. في بعض الحالات ، قد توفر القوانين واللوائح المحلية حماية أكبر من تلك الموصوفة في هذه السياسة. </p>

                <p> 2. المضايقة </p>

                <p> MI Postwares ، LLC ملتزم بتوفير بيئة عمل خالية من المضايقات. أي سلوك غير مرغوب فيه ومسيء للمستلم ، والذي يخلق بيئة عمل تخويف أو معادية أو مهينة لهذا الشخص ينتهك سياسة MI Postwares ، LLC. يمكن أن تحدث المضايقة بين أعضاء الجنس الآخر أو نفس الجنس. التحرش ، اللفظي أو غير لفظي ، صريح أو ضمني ، بناءً على جنس الفرد أو العرق أو العرق أو الأصل القومي أو العمر أو الدين أو أي خصائص أخرى محمية قانونًا. يُطلب من جميع الموظفين ، بمن فيهم المشرفون ، وموظفي الإدارة الآخرين ، والمقاولين المستقلين ، الالتزام بهذه السياسة. لن يتأثر أي شخص سلبًا في العمل مع MI Softwares ، LLC نتيجة لتقديم شكاوى من المضايقات. </p>

                <ص>3. التحرش الجنسي </p>

                <p> التقدم الجنسي غير المرغوب فيه ، وطلبات الحسنات الجنسية ، وغيرها من السلوك اللفظي أو الجسدي للطبيعة الجنسية تشكل مضايقة عندما يتم تقديم (1) تقديم مثل هذا السلوك إما بشكل صريح أو ضمني مصطلح أو حالة توظيف ؛ (2) يتم استخدام أو رفض هذا السلوك من قبل الفرد كأساس لقرارات التوظيف أو الترقية أو النقل أو الاختيار للتدريب أو تقييمات الأداء أو الفوائد أو غيرها من الشروط والأحكام الوظيفية ؛ أو (3) هذا السلوك له غرض أو تأثير إنشاء بيئة عمل تخويف أو معادية أو مسيئة أو يتداخل بشكل كبير مع أداء عمل الموظف. تحظر MI Softwares ، LLC السلوك غير المناسب الذي هو جنسي بطبيعته في العمل ، أو في أعمال الشركة ، أو في الأحداث التي ترعاها الشركة بما في ذلك ما يلي: التعليقات ، النكات ، اللغة المهينة ، الأشياء المقترحة جنسيًا ، أو الكتب ، أو أي شكل من أشكال الوسائط الإلكترونية أو في نموذج الطباعة. يحظر التحرش الجنسي سواء كان ذلك بين أعضاء الجنس الآخر أو أعضاء نفس الجنس. </p>

                <p> 4. بيان عن العمل الإيجابي </p>

                <p> تم تطوير برنامج عمل إيجابي حيث تسعى Mi Softwares ، LLC إلى زيادة تمثيل الأقليات ومشاركتها </p>

                <p> 5. الإبلاغ عن التمييز والتحرش </p>

                <p> إذا شعر الموظف أنه أو هي مضايقة كما هو موضح في هذه السياسة ، فيجب عليهم تقديم التظلم على الفور مع: قسم التظلم ، 6255 TownCenter Drive ، Ste 819 ، Clemmons NC 27012 ، أو عن طريق البريد الإلكتروني في Complience@Mi Softwares. نحن. بمجرد الإبلاغ عن الأمر ، سيتم التحقيق على الفور وسيتم اتخاذ أي إجراء تصحيحي عند اعتباره مناسبًا. جميع الشكاوى أو المضايقات غير القانونية بموجب هذه السياسة أو سيتم التعامل معها بطريقة سرية قدر الإمكان. يتم تشجيع الإبلاغ في الوقت المناسب لمنع إعادة تواجد السلوك أو المعالجة بطريقة أخرى هذه السياسة أو القانون. يمكن أن يحد التأخير في الإبلاغ عن الشكوى من نوع فعالية الاستجابة من قبل MI Softwares ، LLC. لا يهدف إجراء الإبلاغ عن حوادث السلوك التمييزي أو المضايق إلى منع حق أي موظف في طلب علاج بموجب قانون الولاية أو القانون الفيدرالي المتاح من خلال الإبلاغ على الفور عن المسألة إلى الوكالة الفيدرالية المناسبة. </p>

                <p> 6. الانتقام </p>

                <p> الانتقام من أي شخص مرتبط بـ MI Postwares ، LLC الذي يبلغ عن حالات من المضايقات - سواء كان هو أو هي متورط بشكل مباشر أو غير مباشر - ينتهك سياسات MI Softwares ، LLC. من المفترض أن يتم إجراء جميع الحوادث المبلغ عنها بحسن نية. سيتم التعامل مع أي ادعاءات مثبتة كاذبة على أنها مسألة خطيرة. </p>

                <p> 7. التدابير التأديبية للمضايقة </p>

                <p> أي موظف يشارك في السلوك الذي ينتهك هذه السياسة سيخضع لاتخاذ إجراءات تأديبية ، بما في ذلك الإنهاء المحتمل للتوظيف ، سواء تم انتهاك قانون فعلي أم لا. </p>

                <p> 8. العلاجات </p>

                <p> سبل الانتصاف لأي حالات من التمييز العملي الذي تم التحقق منه ، سواء كان ذلك عن قصد أو عن طريق الإجراءات التي لها تأثير تمييزي ، قد تشمل الأجور الظهر ، التوظيف ، الترويج ، إعادة ، رواتب أمامية ، أماكن إقامة معقولة ، أو إجراءات أخرى تُعتبر مناسبة من قبل MI Softwares ، ذ م م. يمكن أن تشمل العلاجات أيضًا دفع أتعاب المحاماة ورسوم الشهود الخبراء وتكاليف المحكمة والرسوم القانونية الأخرى المعمول بها. </p>

                <p> 9. تنفيذ السياسة </p>

                <p> الرئيس التنفيذي لشركة Mi Softwares ، Lynn Graham ، يدعم تمامًا تنفيذ هذه السياسة الفعالة اعتبارًا من 19 أبريل 2021. </p>', 
            'dmv_title' => 'DMV Check' ,
            'dmv' => '<h2> <strong> فحص DMV والتحقق من الخلفية الموافقة </strong> </h2>

                <p> </p>

                <p> موافقة على طلب سجل القيادة </p>

                <p> أفهم أن Mi Softwares ، LLC. ("الشركة") ستستخدم Checkr. ، ("Checkr ، Inc.") للحصول على سجل للسيارات كجزء من عملية التقديم ليكون سائقًا على منصة Mi Postwares ("برنامج التشغيل"). أفهم أيضًا أنه إذا تم قبولها كسائق ، إلى الحد الذي يسمح به القانون ، فيجوز للشركة الحصول على تقارير أخرى من CheckR Inc. من أجل تحديث أو تمديد أو تمديد حالتي كسائق. أعطي بموجب هذا الإذن لبرامج MI للحصول على سجل قيادة الولاية الخاص بي (المعروف أيضًا باسم سجل سيارتي أو MVR) وفقًا لقانون حماية خصوصية السائق الفيدرالي ("DPPA" وقانون الولاية المعمول به. أقر وأفهم أن سجل القيادة الخاص بي هو تقرير المستهلك الذي يحتوي على معلومات السجل العام. أسمح ، دون حجز أي طرف أو وكالة تم الاتصال بها من قبل شركة أو CheckR Inc. لتزويد الشركة بنسخة من سجل قيادة الولاية الخاص بي. يجب أن يظل هذا التفويض في ملف من قبل الشركة طوال فترة وقتي كسائق ، وسيكون بمثابة ترخيص مستمر للشركة لشراء سجل قيادة الولاية في أي وقت أثناء قيامي. </p>

                <p> موافقة على طلب تقرير المستهلك أو معلومات تقرير المستهلك الاستقصائي </p>

                <p> أفهم أن Mi Softwares ، LLC. ("الشركة") سوف تستخدم Checkr Inc. ، </p>

                <p> 1 Montgomery St ، Ste 2000 ، San Francisco ، CA 94104 </p>

                <p> للحصول على تقرير المستهلك أو تقرير المستهلك الاستقصائي كجزء من عملية التقديم ليكون محركًا على منصة Mi Softwares ("برنامج التشغيل"). أفهم أيضًا أنه إذا تم قبولها كسائق ، إلى الحد الذي يسمح به القانون ، يجوز للشركة الحصول على المزيد من التقارير من CheckR من أجل تحديث أو تمديد أو تمديد حالتي كبرنامج سائق. </p>

                <p> أفهم أن التحقيق في CheckR ، Inc ("CheckR") قد يشمل الحصول على معلومات بشأن السجل الجنائي الخاص بي ، مع مراعاة أي قيود يفرضها قانون الفيدرالية والقانون المعمول بها. أفهم أن هذه المعلومات قد يتم الحصول عليها من خلال الاتصال المباشر أو غير المباشر مع الوكالات العامة أو الأشخاص الآخرين الذين قد يكون لديهم مثل هذه المعرفة. </p>

                <p> ستشمل طبيعة ونطاق التحقيق المطلوب فحص خلفية جنائية وتتبع SSN. </p>

                <p> أقر باستلام الملخص المرفق لحقوق بلدي بموجب قانون الإبلاغ عن الائتمان العادل ، وكما هو مطلوب بموجب القانون ، أي ملخص لحقوق ذات صلة ("ملخصات الحقوق"). </p>

                <p> لن تؤثر هذه الموافقة على قدرتي على التشكيك في دقة أي معلومات واردة في تقرير. أنا أفهم ما إذا كانت الشركة اتخذت قرارًا مشروطًا بعدم تأهيل كل أو جزئيًا على تقريري ، فسيتم تزويدي بنسخة من التقرير ونسخة أخرى من ملخصات الحقوق ، وإذا كنت لا أتفق مع دقة عدم الأهلية المزعومة المعلومات الواردة في التقرير ، يجب أن أخطر الشركة في غضون خمسة أيام عمل عن استلامي للتقرير بأنني أتحدى دقة هذه المعلومات باستخدام Checkr. </p>

                <p> أوافق بموجب هذا على هذا التحقيق وأسمح للشركة بشراء تقرير عن خلفيتي. </p>

                <p> من أجل التحقق من هويتي لأغراض إعداد التقارير ، أقوم طوعًا بإطلاق تاريخ ميلادي ورقم الضمان الاجتماعي والمعلومات الأخرى وأفهم تمامًا أن جميع القرارات تستند إلى أسباب غير تمييزية مشروعة. </p >

                <p> اسم وعنوان ورقم الهاتف لأقرب وحدة من وكالة التقارير المستهلك المعينة للتعامل مع الاستفسارات المتعلقة بتقرير المستهلك الاستقصائي هو: </p>

                <p> <strong> Checkr ، Inc. <Br />
                1 Montgomery St ، Ste 2000 ، San Francisco ، CA 94104 <BR />
                844-824-3257 </strong> <br />
                <br />
                <strong> California و Maine و Massachusetts و Minnesota و New Jersey & Oklahoma Mustomants فقط: </strong> لدي الحق في طلب نسخة من أي تقرير تم الحصول عليه بواسطة الشركة من ChectR عن طريق التحقق من المربع. (تحقق فقط إذا كنت ترغب في تلقي نسخة) </p>

                <p> المتقدمون في نيويورك فقط </p>

                <p> أقر أيضًا أنني تلقيت نسخة مرفقة من المادة 23A من قانون تصحيح نيويورك. أدرك كذلك أنني قد أطلب نسخة من أي تقرير مستهلك استقصائي عن طريق الاتصال بـ CheckR. أدرك أيضًا أنه سيتم إخباري إذا تم طلب أي شيكات أخرى وقدم اسم وعنوان وكالة الإبلاغ المستهلك. </p>

                <p> المتقدمين والمقيمين في كاليفورنيا </p>

                <p> إذا كنت أتقدم في كاليفورنيا أو أقيمت في كاليفورنيا ، فأنا أفهم أن لدي الحق في فحص الملفات المتعلقة بي بصريًا بشأن وكالة تقارير للمستهلكين خلال ساعات العمل العادية وعند إشعار معقول. يمكن إجراء التفتيش شخصيًا ، وإذا ظهرت شخصيًا وأقدم تحديدًا مناسبًا ؛ يحق لي الحصول على نسخة من الملف مقابل رسوم لا تتجاوز التكاليف الفعلية للتكرار. يحق لي أن أكون برفقة شخص واحد من اختياري ، والذي يوفر هوية معقولة. يمكن أيضًا إجراء التفتيش عبر البريد المعتمد إذا قمت بتقديم طلب مكتوب ، مع تحديد هوية مناسب ، لإرسال النسخ إلى المرسل إليه المحدد. يمكنني أيضًا أن أطلب ملخصًا للمعلومات التي يتم توفيرها عن طريق الهاتف إذا قمت بتقديم طلب مكتوب ، مع تحديد هوية مناسب للكشف عبر الهاتف ، ورسوم Toll ، إن وجدت ، للمكالمة الهاتفية مدفوعة مسبقًا أو مشحونة مباشرةً لي. أدرك أيضًا أن وكالة الإبلاغ عن المستهلكين التحقريين يجب أن توفر لموظفي مدربين لشرح لي أي من المعلومات المقدمة لي ؛ سأتلقى من وكالة الإبلاغ عن المستهلك الاستقصائي شرحًا مكتوبًا لأي معلومات مشفرة واردة في الملفات المحفوظة علي. "التعريف الصحيح" كما هو مستخدم في هذه الفقرة يعني أن المعلومات تعتبر كافية بشكل عام لتحديد الشخص ، بما في ذلك المستندات مثل رخصة القيادة الصالحة ورقم حساب الضمان الاجتماعي وبطاقات الهوية العسكرية وبطاقات الائتمان. أتفهم أنه يمكنني الوصول إلى خصوصية موقع الويب التالي CheckR.com لعرض ممارسات خصوصية CheckR ، بما في ذلك المعلومات فيما يتعلق بإعداد CheckR ومعالجته لتقارير المستهلكين والتحقيق حول ما إذا كان سيتم إرسال معلوماتي الشخصية خارج الولايات المتحدة أو أراضيها . </p>

                <p> ملخص لحقوقك بموجب قانون الإبلاغ عن الائتمان العادل </p>

                <p> يعزز قانون الإبلاغ عن الائتمان العادل الفيدرالي (FCRA) دقة وخصوصية وخصوصية المعلومات في ملفات وكالات الإبلاغ عن المستهلك. هناك العديد من أنواع وكالات الإبلاغ عن المستهلكين ، بما في ذلك مكاتب الائتمان والوكالات التخصصية (مثل الوكالات التي تبيع معلومات حول تاريخ كتابة الشيكات والسجلات الطبية وسجلات تاريخ الإيجار). فيما يلي ملخص لحقوقك الرئيسية بموجب FCRA. <strong> لمزيد من المعلومات ، بما في ذلك المعلومات حول الحقوق الإضافية ، انتقل إلى www.consumerfinance.gov/learnmore أو اكتب إلى: </strong> </p>

                <p> مكتب الحماية المالية للمستهلك <br />
                1700 G Street NW ، Washington ، DC 20552 </p>

                <p> </p>

                <ul>
                <li> يجب أن يتم إخبارك إذا تم استخدام المعلومات الموجودة في ملفك ضدك. يجب على أي شخص يستخدم تقرير ائتمان أو نوع آخر من تقرير المستهلك لرفض طلبك للحصول على الائتمان أو التأمين أو التوظيف - أو لاتخاذ إجراءات سلبية أخرى ضدك - ويجب أن يعطيك اسم وعنوان ورقم هاتفه الوكالة التي قدمت المعلومات. </li>
                <li> لديك الحق في معرفة ما هو موجود في ملفك. يمكنك طلب والحصول على جميع المعلومات الخاصة بك في ملفات وكالة الإبلاغ عن المستهلك ("الكشف عن الملف"). سيُطلب منك تقديم الهوية المناسبة ، والتي قد تتضمن رقم الضمان الاجتماعي الخاص بك. في كثير من الحالات ، سيكون الكشف مجانيًا. يحق لك الحصول على إفصاح مجاني للملف إذا:
                <ol>
                <li> اتخذ الشخص إجراءً سلبياً ضدك بسبب المعلومات في تقرير الائتمان الخاص بك ؛ </li>
                <li> أنت ضحية لسرقة الهوية ووضع تنبيه الاحتيال في ملفك ؛ </li>
                <li> يحتوي ملفك على معلومات غير دقيقة نتيجة للاحتيال ؛ </li>
                <li> أنت على المساعدة العامة ؛ </li>
                <li> أنت عاطل عن العمل ولكنك تتوقع التقدم بطلب للحصول على عمل في غضون 60 يومًا. </li>
                </ol>
                بالإضافة إلى ذلك ، يحق لجميع المستهلكين الحصول على إفصاح مجاني واحد كل 12 شهرًا عند الطلب من كل مكتب الائتمان على مستوى البلاد ومن وكالات التقارير للمستهلكين المتخصصة على مستوى البلاد. انظر www.consumerfinance.gov/learnmore للحصول على معلومات إضافية. </li>
                <li> لديك الحق في طلب درجة الائتمان. Credit scores are numerical summaries of your credit-worthiness based on information from credit bureaus. You may request a credit score from consumer reporting agencies that create scores or distribute scores used in residential real property loans, but you will have to pay for it. In some mortgage transactions, you will receive credit score information for free from the mortgage lender.</li>
                <li>You have the right to dispute incomplete or inaccurate information. If you identify information in your file that is incomplete or inaccurate, and report it to the consumer reporting agency, the agency must investigate unless your dispute is frivolous. See www.consumerfinance.gov/learnmore for an explanation of dispute procedures.</li>
                <li>Consumer reporting agencies must correct or delete inaccurate, incomplete, or unverifiable information. Inaccurate, incomplete or unverifiable information must be removed or corrected, usually within 30 days. However, a consumer reporting agency may continue to report information it has verified as accurate.</li>
                <li>Consumer reporting agencies may not report outdated negative information. In most cases, a consumer reporting agency may not report negative information that is more than seven years old, or bankruptcies that are more than 10 years old.</li>
                <li>Access to your file is limited. A consumer reporting agency may provide information about you only to people with a valid need – usually to consider an application with a creditor, insurer, employer, landlord, or other business. The FCRA specifies those with a valid need for access.</li>
                <li>You must give your consent for reports to be provided to employers. A consumer reporting agency may not give out information about you to your employer, or a potential employer, without your written consent given to the employer. Written consent generally is not required in the trucking industry. For more information, go to www.consumerfinance.gov/learnmore</li>
                <li>You may limit “prescreened” offers of credit and insurance you get based on information in your credit report. Unsolicited “prescreened” offers for credit and insurance must include a toll-free phone number you can call if you choose to remove your name and address from the lists these offers are based on. You may opt-out with the nationwide credit bureaus at 1-888-567-8688.</li>
                <li>You may seek damages from violators. If a consumer reporting agency, or, in some cases, a user of consumer reports or a furnisher of information to a consumer reporting agency violates the FCRA, you may be able to sue in state or federal court.</li>
                <li>Identity theft victims and active duty military personnel have additional rights. For more information, visit www.consumerfinance.gov/learnmore.</li>
                </ul>

                <p>States may enforce the FCRA, and many states have their own consumer reporting laws. In some cases, you may have more rights under state law. For more information, contact your state or local consumer protection agency or your state Attorney General. For information about your federal rights, contact:</p>

                <p> </p>

                <table>
                <thead>
                <tr>
                <th>
                <p>Type of business</p>
                </th>
                <th>
                <p>Contact</p>
                </th>
                </tr>
                </thead>
                <الجسم>
                <tr>
                <td>1.a. Banks, savings associations, and credit unions with total assets of over $10 billion and their affiliates.</td>
                <td>a. Consumer Financial Protection Bureau 1700 G Street NW, Washington, DC 20552</td>
                </tr>
                <tr>
                <td>1.b. Such affiliates that are not banks, savings associations, or credit unions also should list, in addition to the CFPB:</td>
                <td>b. Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td colspan="2">
                <p>To the extent not included in item 1 above</p>
                </TD>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>2.a. National banks, federal savings associations, and federal branches and federal agencies of foreign banks</td>
                <td>a. Office of the Comptroller of the Currency Customer Assistance Group 1301 McKinney Street Suite 3450, Houston, TX 77010-9050</td>
                </tr>
                <tr>
                <td>2.b. State member banks, branches and agencies of foreign banks (other than federal branches, federal agencies, and Insured State Branches of Foreign Banks), commercial lending companies owned or controlled by foreign banks, and organizations operating under section 25 or 25A of the Federal Reserve Act</td>
                <td>b. Federal Reserve Consumer Help Center P.O. Box 1200 Minneapolis, MN 55480</td>
                </tr>
                <tr>
                <td>2.c. Nonmember Insured Banks, Insured State Branches of Foreign Banks, and insured state savings associations</td>
                <td>c. FDIC Consumer Response Center 1100 Walnut Street Box #11, Kansas City, MO 64106</td>
                </tr>
                <tr>
                <td>2.d. Federal Credit Unions</td>
                <td>d. National Credit Union Administration Office of Consumer Protection (OCP), Division of Consumer Compliance and Outreach (DCCO) 1775 Duke Street, Alexandria, VA 22314</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>3. Air carriers</td>
                <td>Asst. General Counsel for Aviation Enforcement & Proceedings Aviation Consumer Protection Division Department of Transportation 1200 New Jersey Avenue SE, Washington, DC 20590</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>4. Creditors Subject to Surface Transportation Board</td>
                <td>Office of Proceedings, Surface Transportation Board, Department of Transportation 395 E Street SW, Washington, DC 20423</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>5. Creditors Subject to Packers and Stockyards Act, 1921</td>
                <td>Nearest Packers and Stockyards Administration area supervisor</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>6. Small Business Investment Companies</td>
                <td>Associate Deputy Administrator for Capital Access, United States Small Business Administration 409 Third Street SW 8th Floor, Washington, DC 20416</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>7. Brokers and Dealers</td>
                <td>Securities and Exchange Commission 100 F St NE, Washington, DC 20549</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>8. Federal Land Banks, Federal Land Bank Associations, Federal Intermediate Credit Banks, and Production Credit Associations</td>
                <td>Farm Credit Administration, 1501 Farm Credit Drive, McLean, VA 22102-5090</td>
                </tr>
                </tbody>
                <الجسم>
                <tr>
                <td>9. Retailers, Finance Companies, and All Other Creditors Not Listed Above</td>
                <td>FTC Regional Office for region in which the creditor operates or Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                </table>',
            'locale' => 'ar',
            'language' => 'Arabic',
            'direction' => 'rtl',
        ],
         // french
         [
            'id' => Str::uuid(),
            'privacy_title' => 'Politique de confidentialité',
            'privacy' =>'<h2>Politique de confidentialité</h2>

                <p>La portée de cette politique</p>
                
                <p>Cette politique sapplique à tous les utilisateurs de Mi Softwares, y compris les pilotes et les pilotes (y compris les candidats aux pilotes), ainsi quà toutes les plates-formes et services de Mi Softwares, y compris nos applications, sites Web, fonctionnalités et autres services (collectivement, la « Plateforme Mi Softwares). »). Noubliez pas que votre utilisation de la plateforme Mi Softwares est également soumise à nos conditions dutilisation.</p>
                
                <p>Les informations que nous collectons</p>
                
                <p>Lorsque vous utilisez la plateforme Mi Softwares, nous collectons les informations que vous fournissez, les informations dutilisation et les informations sur votre appareil. Nous collectons également des informations vous concernant à partir dautres sources telles que des services tiers et des programmes facultatifs auxquels vous participez, que nous pouvons combiner avec dautres informations dont nous disposons sur vous. Voici les types dinformations que nous collectons à votre sujet :</p>
                
                <p>A. Informations que vous nous fournissez</p>
                
                <p>Enregistrement du compte. Lorsque vous créez un compte avec Mi Softwares, nous collectons les informations que vous nous fournissez, telles que votre nom, votre adresse e-mail, votre numéro de téléphone et vos informations de paiement. Vous pouvez choisir de partager des informations supplémentaires avec nous pour votre profil Rider, comme votre photo ou vos adresses enregistrées (par exemple, domicile ou travail), et configurer dautres préférences (telles que vos pronoms préférés).<br />
                <br />
                <strong>Informations sur le conducteur.</strong> Si vous postulez pour devenir chauffeur, nous collecterons les informations que vous fournissez dans votre candidature, notamment votre nom, votre adresse e-mail, votre numéro de téléphone, votre date de naissance, votre photo de profil, votre adresse physique, votre pièce didentité gouvernementale. numéro (tel que le numéro de sécurité sociale), informations sur le permis de conduire, informations sur le véhicule et informations sur lassurance automobile. Nous collectons les informations de paiement que vous nous fournissez, y compris vos numéros de routage bancaire et vos informations fiscales. En fonction de lendroit où vous souhaitez conduire, nous pouvons également demander des informations supplémentaires sur une licence commerciale ou un permis ou dautres informations pour gérer la conduite et les programmes pertinents à cet endroit. Nous pouvons avoir besoin dinformations supplémentaires de votre part à un moment donné après que vous soyez devenu conducteur, y compris des informations pour confirmer votre identité (comme une photo).<br />
                <br />
                <strong>Informations de contact SOS</strong> Nous collecterons lautorisation de la liste de contacts pour répertorier les contacts lors de lajout des contacts SOS pour lapplication utilisateur et pilote<br />
                <br />
                <strong>Notes et commentaires.</strong> Lorsque vous évaluez et fournissez des commentaires sur des passagers ou des chauffeurs, nous collectons toutes les informations que vous fournissez dans vos commentaires.<br />
                <br />
                <strong>Communications.</strong> Lorsque vous nous contactez ou que nous vous contactons, nous collectons toutes les informations que vous fournissez, y compris le contenu des messages ou des pièces jointes que vous nous envoyez.</p>
                
                <p>B. Informations que nous collectons lorsque vous utilisez la plateforme Mi Softwares</p>
                
                <p><strong>Informations de localisation.</strong> Les bonnes balades commencent par une prise en charge facile et précise. La plate-forme Mi Softwares collecte des informations de localisation (y compris les données GPS et WiFi) différemment en fonction des paramètres de votre application Mi Softwares et des autorisations de votre appareil, ainsi que selon que vous utilisez la plate-forme en tant que pilote ou pilote :</p>
                
                <ul>
                <li>Passeurs : nous collectons la position précise de votre appareil lorsque vous ouvrez et utilisez lapplication Mi Softwares, y compris lorsque lapplication sexécute en arrière-plan, à partir du moment où vous demandez un trajet jusquà sa fin. Mi Softwares suit également à tout moment lemplacement précis des scooters et des vélos électriques.</li>
                <li>Pilotes : nous collectons la position précise de votre appareil lorsque vous ouvrez et utilisez lapplication, y compris lorsque lapplication sexécute en arrière-plan lorsquelle est en mode pilote. Nous collectons également une position précise pendant une durée limitée après que vous ayez quitté le mode conducteur afin de détecter les incidents de trajet, et continuons à la collecter jusquà ce quun incident signalé ou détecté ne soit plus actif.</li>
                </ul>
                
                <p><strong>Informations dutilisation.</strong> Nous collectons des informations sur votre utilisation de la plateforme Mi Softwares, y compris des informations sur le trajet telles que la date, lheure, la destination, la distance, litinéraire, le paiement et si vous avez utilisé une promotion ou une référence. code. Nous collectons également des informations sur vos interactions avec la plateforme Mi Softwares, comme nos applications et nos sites Web, y compris les pages et le contenu que vous consultez ainsi que les dates et heures de votre utilisation.<br />
                <br />
                <strong>Informations sur lappareil.</strong> Nous collectons des informations sur les appareils que vous utilisez pour accéder à la plate-forme Mi Softwares, notamment le modèle de lappareil, ladresse IP, le type de navigateur, la version du système dexploitation, lidentité de lopérateur et du fabricant, le type de radio ( tels que la 4G), les préférences et paramètres (tels que la langue préférée), les installations dapplications, les identifiants dappareil, les identifiants publicitaires et les jetons de notification push. Si vous êtes un conducteur, nous collectons également des données de capteurs mobiles à partir de votre appareil (telles que la vitesse, la direction, la hauteur, laccélération, la décélération et dautres données techniques).<br />
                <br />
                <strong>Communications entre les coureurs et les chauffeurs.</strong> Nous travaillons avec un tiers pour faciliter les appels téléphoniques et les messages texte entre les coureurs et les chauffeurs sans partager le numéro de téléphone réel de lune ou lautre partie avec lautre. Mais même si nous faisons appel à un tiers pour fournir le service de communication, nous collectons des informations sur ces communications, notamment les numéros de téléphone des participants, la date et lheure, ainsi que le contenu des messages SMS. Pour des raisons de sécurité, nous pouvons également surveiller ou enregistrer le contenu des appels téléphoniques passés via la plateforme Mi Softwares, mais nous vous informerons toujours que nous sommes sur le point de le faire avant le début de lappel.<br />
                <br />
                <strong>Contacts du carnet dadresses.</strong> Vous pouvez définir les autorisations de votre appareil pour accorder à Mi Softwares laccès à vos listes de contacts et demander à Mi Softwares daccéder à votre liste de contacts, par exemple pour vous aider à parrainer des amis vers Mi Softwares. Si vous faites cela, nous accéderons et stockerons les noms et coordonnées des personnes figurant dans votre carnet dadresses.<br />
                <br />
                <strong>Cookies, analyses et technologies tierces.</strong> Nous collectons des informations via lutilisation de « cookies », de pixels de suivi, doutils danalyse de données tels que Google Analytics, de SDK et dautres technologies tierces pour comprendre comment vous naviguez sur la plateforme Mi Softwares et interagissez avec les publicités Mi Softwares, pour rendre votre expérience Mi Softwares plus sûre, pour savoir quel contenu est populaire, pour améliorer votre expérience sur le site, pour vous proposer de meilleures publicités sur dautres sites et pour enregistrer vos préférences. Les cookies sont de petits fichiers texte que les serveurs Web placent sur votre appareil ; ils sont conçus pour stocker des informations de base et pour aider les sites Web et les applications à reconnaître votre navigateur. Nous pouvons utiliser à la fois des cookies de session et des cookies persistants. Un cookie de session disparaît après la fermeture de votre navigateur. Un cookie persistant reste après la fermeture de votre navigateur et est accessible chaque fois que vous utilisez la plateforme Mi Softwares. Vous devez consulter votre (vos) navigateur(s) Web pour modifier vos paramètres de cookies. Veuillez noter que si vous supprimez ou choisissez de ne pas accepter nos cookies, vous risquez de manquer certaines fonctionnalités de la plateforme Mi Softwares.</p>
                
                <p>C. Informations que nous collectons auprès de tiers</p>
                
                <p><strong>Services tiers.</strong> Les services tiers nous fournissent les informations nécessaires pour les aspects essentiels de la plate-forme Mi Softwares, ainsi que pour des services, programmes, avantages de fidélité et promotions supplémentaires qui peuvent améliorer votre expérience Mi Softwares. Ces services tiers incluent des prestataires de vérification des antécédents, des partenaires dassurance, des prestataires de services financiers, des prestataires de marketing et dautres entreprises. Nous obtenons les informations suivantes vous concernant auprès de ces services tiers :</p>
                
                <ul>
                <li>Informations visant à rendre la plate-forme Mi Softwares plus sûre, comme les informations de vérification des antécédents des conducteurs ;</li>
                <li>Informations sur votre participation à des programmes tiers qui fournissent des éléments tels quune couverture dassurance et des instruments financiers, tels que des informations sur lassurance, les paiements, les transactions et la détection des fraudes ;</li>
                <li>Informations permettant de mettre en œuvre des programmes ou des applications, services ou fonctionnalités de fidélité et promotionnels que vous choisissez de connecter ou de lier à votre compte Mi Softwares, telles que des informations sur votre utilisation de ces programmes, applications, services ou fonctionnalités ; et</li>
                <li>Informations vous concernant fournies par des services spécifiques, telles que des informations démographiques et sur les segments de marché.</li>
                </ul>
                
                <p><strong>Programmes dentreprise.</strong> Si vous utilisez Mi Softwares par lintermédiaire de votre employeur ou dune autre organisation qui participe à lun de nos programmes dentreprise Mi Softwares Business, nous collecterons des informations vous concernant auprès de ces parties, telles que votre nom. et coordonnées.<br />
                <br />
                <strong>Service de conciergerie.</strong> Parfois, une autre entreprise ou entité peut vous commander un trajet Mi Softwares. Si une organisation a commandé un trajet pour vous en utilisant notre service de conciergerie, elle nous fournira vos coordonnées ainsi que le lieu de prise en charge et de dépôt de votre trajet.<br />
                <br />
                <strong>Programmes de parrainage.</strong> Les amis aident leurs amis à utiliser la plateforme Mi Softwares. Si quelquun vous recommande Mi Softwares, nous collecterons des informations vous concernant à partir de cette référence, notamment votre nom et vos coordonnées.<br />
                <br />
                <strong>Autres utilisateurs et sources.</strong> Dautres utilisateurs ou sources publiques ou tierces telles que les forces de lordre, les assureurs, les médias ou les piétons peuvent nous fournir des informations vous concernant, par exemple dans le cadre dune enquête sur un incident ou pour vous apporter votre soutien.</p>
                
                <p>Comment nous utilisons vos informations</p>
                
                <p>Nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Fournir la plate-forme Mi Softwares ;</li>
                <li>Maintenir la sécurité et la sûreté de la plate-forme Mi Softwares et de ses utilisateurs ;</li>
                <li>Créer et maintenir la communauté Mi Softwares ;</li>
                <li>Fournir un support client ;</li>
                <li>Améliorer la plate-forme Mi Softwares ; et</li>
                <li>Répondre aux procédures et obligations juridiques.</li>
                </ul>
                
                <p><strong>Fournir la plateforme Mi Softwares.</strong> Nous utilisons vos informations personnelles pour offrir une expérience intuitive, utile, efficace et intéressante sur notre plateforme. Pour ce faire, nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Vérifiez votre identité et gérez votre compte, vos paramètres et vos préférences ;</li>
                <li>Vous connecter à vos courses et suivre leur progression ;</li>
                <li>Calculer les prix et traiter les paiements ;</li>
                <li>Autoriser les passagers et les conducteurs à se connecter concernant leur trajet et à choisir de partager leur position avec dautres ;</li>
                <li>Communiquer avec vous sur vos courses et votre expérience ;</li>
                <li>Recueillir des commentaires concernant votre expérience ;</li>
                <li>Faciliter des services et des programmes supplémentaires avec des tiers ; et</li>
                <li>Organisez des concours, des tirages au sort et dautres promotions.</li>
                </ul>
                
                <p><strong>Maintenir la sécurité et la sûreté de la plateforme Mi Softwares et de ses utilisateurs.</strong> Vous offrir une expérience sécurisée et sécurisée est le moteur de notre plateforme, à la fois sur la route et sur nos applications. Pour ce faire, nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Authentifier les utilisateurs ;</li>
                <li>Vérifier que les chauffeurs et leurs véhicules répondent aux exigences de sécurité ;</li>
                <li>Enquêter et résoudre les incidents, les accidents et les réclamations dassurance ;</li>
                <li>Encourager un comportement de conduite sûr et éviter les activités dangereuses ;</li>
                <li>Détecter et prévenir la fraude ; et</li>
                <li>Bloquez et supprimez les utilisateurs dangereux ou frauduleux de la plate-forme Mi Softwares.</li>
                </ul>
                
                <p><strong>Créer et entretenir la communauté Mi Softwares.</strong> Mi Softwares sefforce dêtre un élément positif de la communauté. Nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Communiquer avec vous sur les événements, les promotions, les élections et les campagnes ;</li>
                <li>Personnaliser et fournir du contenu, des expériences, des communications et de la publicité pour promouvoir et développer la plate-forme Mi Softwares ; et</li>
                <li>Aidez à faciliter les dons que vous choisissez de faire via la plate-forme Mi Softwares.</li>
                </ul>
                
                <p><strong>Fournir une assistance client.</strong> Nous travaillons dur pour offrir la meilleure expérience possible, notamment en vous aidant lorsque vous en avez besoin. Pour ce faire, nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Enquêter et vous aider à résoudre vos questions ou problèmes concernant la plate-forme Mi Softwares ; et</li>
                <li>Vous fournir une assistance ou vous répondre.</li>
                </ul>
                
                <p><strong>Amélioration de la plateforme Mi Softwares</strong>. Nous travaillons toujours à améliorer votre expérience et à vous fournir de nouvelles fonctionnalités utiles. Pour ce faire, nous utilisons vos informations personnelles pour :</p>
                
                <ul>
                <li>Effectuer des recherches, des tests et des analyses ;</li>
                <li>Développer de nouveaux produits, fonctionnalités, partenariats et services ;</li>
                <li>Prévenir, rechercher et résoudre les bugs et problèmes logiciels ou matériels ; et</li>
                <li>Surveiller et améliorer nos opérations et nos processus, y compris les pratiques de sécurité, les algorithmes et autres modèles.</li>
                </ul>
                
                <p><strong>Répondre aux procédures et exigences juridiques.</strong> Parfois, la loi, les entités gouvernementales ou dautres organismes de réglementation nous imposent des demandes et des obligations en ce qui concerne les services que nous cherchons à fournir. Dans une telle circonstance, nous pouvons utiliser vos informations personnelles pour répondre à ces demandes ou obligations.</p>
                
                <p>Comment nous partageons vos informations</p>
                
                <p>Nous ne vendons pas vos informations personnelles. Pour faire fonctionner la plateforme Mi Softwares, nous pouvons être amenés à partager vos informations personnelles avec dautres utilisateurs, des tiers et des fournisseurs de services. Cette section explique quand et pourquoi nous partageons vos informations.</p>
                
                <p>A. Partage entre les utilisateurs des logiciels Mi</p>
                
                <p>Coureurs et chauffeurs.<br />
                <br />
                <strong>Informations sur le passager partagées avec le chauffeur :</strong> Dès réception dune demande de trajet, nous partageons avec le chauffeur le lieu de prise en charge du passager, son nom, sa photo de profil, sa note et ses statistiques (comme le nombre approximatif de trajets et dannées en tant que passager). , et les informations que le coureur inclut dans son profil de coureur (comme les pronoms préférés). Lors de la prise en charge et pendant le trajet, nous partageons avec le chauffeur la destination du passager et tout arrêt supplémentaire que le passager saisit dans lapplication Mi Softwares. Une fois le trajet terminé, nous partageons également éventuellement la note et les commentaires du pilote avec le chauffeur. (Nous supprimons lidentité du Passager associée aux notes et aux commentaires lorsque nous la partageons avec les Chauffeurs, mais un Chauffeur peut être en mesure didentifier le Passager qui a fourni la note ou les commentaires.)<br />
                <br />
                <strong>Informations du conducteur partagées avec le pilote :</strong> lorsquun conducteur accepte une course demandée, nous partagerons avec le pilote le nom du conducteur, sa photo de profil, ses pronoms préférés, sa note, sa localisation en temps réel, ainsi que la marque et le modèle du véhicule. , la couleur et la plaque dimmatriculation, ainsi que dautres informations dans le profil Mi Softwares du conducteur, telles que les informations que les conducteurs choisissent dajouter (comme le drapeau du pays et la raison pour laquelle vous conduisez) et les statistiques du conducteur (comme le nombre approximatif de trajets et dannées en tant que conducteur) .<br />
                <br />
                Bien que nous aidions les passagers et les chauffeurs à communiquer entre eux pour organiser une prise en charge, nous ne partageons pas votre numéro de téléphone réel ou dautres informations de contact avec dautres utilisateurs. Si vous nous signalez un objet perdu ou trouvé, nous chercherons à vous mettre en contact avec le passager ou le chauffeur concerné, y compris en partageant vos coordonnées réelles avec votre consentement.<br />
                <br />
                <strong>Passeurs de trajet partagé.</strong> Lorsque les passagers utilisent un trajet partagé Mi Softwares, nous partageons le nom et la photo de profil de chaque passager pour garantir la sécurité. Les passagers peuvent également voir les lieux de prise en charge et de dépose des uns et des autres afin de connaître litinéraire tout en partageant le trajet.<br />
                <br />
                <strong>Courses demandées ou payées par dautres.</strong> Certaines courses que vous effectuez peuvent être demandées ou payées par dautres. Si vous effectuez lun de ces trajets en utilisant votre compte de profil dentreprise Mi Softwares, un code ou un coupon, un programme subventionné (par exemple, transport en commun ou gouvernement) ou une carte de crédit dentreprise liée à un autre compte, ou si un autre utilisateur demande ou paie autrement un un trajet pour vous, nous pouvons partager tout ou partie des détails de votre trajet avec cette autre partie, y compris la date, lheure, le tarif, la note attribuée, la région du voyage et le lieu de prise en charge et de retour de votre trajet.<br />
                <br />
                <strong>Programmes de parrainage.</strong> Si vous parrainez quelquun vers la plateforme Mi Softwares, nous lui ferons savoir que vous avez généré la parrainage. Si un autre utilisateur vous a recommandé, nous pouvons partager des informations sur votre utilisation de la plateforme Mi Softwares avec cet utilisateur. Par exemple, une source de référence peut recevoir un bonus lorsque vous rejoignez la plateforme Mi Softwares ou effectuez un certain nombre de courses et recevra de telles informations.</p>
                
                <p>B. Partage avec des fournisseurs de services tiers à des fins professionnelles</p>
                
                <p>Selon que vous êtes un pilote ou un conducteur, Mi Softwares peut partager les catégories suivantes de vos informations personnelles à des fins commerciales afin de vous fournir une variété de fonctionnalités et de services de la plateforme Mi Softwares :</p>
                
                <ul>
                <li>Identifiants personnels, tels que votre nom, votre adresse, votre adresse e-mail, votre numéro de téléphone, votre date de naissance, votre numéro didentification gouvernemental (tel que votre numéro de sécurité sociale), les informations de votre permis de conduire, les informations sur votre véhicule et vos informations dassurance automobile ;</li>
                <li>Informations financières, telles que les numéros dacheminement bancaires, les informations fiscales et toute autre information de paiement que vous nous fournissez ;</li>
                <li>Informations commerciales, telles que les informations sur les trajets, les statistiques et commentaires du conducteur/passager, ainsi que lhistorique des transactions du conducteur/passager ;</li>
                <li>Informations sur lactivité Internet ou dautres réseaux électroniques, telles que votre adresse IP, le type de navigateur, la version du système dexploitation, lopérateur et/ou le fabricant, les identifiants de lappareil et les identifiants de publicité mobile ; et</li>
                <li>Données de localisation.</li>
                </ul>
                
                <p>Nous divulguons ces catégories dinformations personnelles à des prestataires de services pour atteindre les objectifs commerciaux suivants :</p>
                
                <ul>
                <li>Maintenir et gérer votre compte Mi Softwares ;</li>
                <li>Traitement ou exécution des courses ;</li>
                <li>Vous fournir un service client ;</li>
                <li>Traitement des transactions Rider ;</li>
                <li>Traitement des demandes et des paiements des chauffeurs ;</li>
                <li>Vérifier lidentité des utilisateurs ;</li>
                <li>Détecter et prévenir la fraude ;</li>
                <li>Traitement des réclamations dassurance ;</li>
                <li>Proposer des programmes de fidélisation et de promotion des chauffeurs ;</li>
                <li>Fournir des services de marketing et de publicité à Mi Softwares ;</li>
                <li>Fournir un financement ;</li>
                <li>Fournir les services durgence demandés ;</li>
                <li>Fournir des services danalyse à Mi Softwares ; et</li>
                <li>Entreprendre des recherches internes pour développer la plate-forme Mi Softwares.</li>
                </ul>
                
                <p>C. Pour des raisons juridiques et pour protéger la plateforme Mi Softwares</p>
                
                <ul>
                <li>Se conformer à toute loi ou réglementation fédérale, étatique ou locale applicable, enquête civile, pénale ou réglementaire, enquête ou procédure judiciaire, ou demande gouvernementale exécutoire ;</li>
                <li>Répondre à une procédure judiciaire (telle quun mandat de perquisition, une assignation à comparaître, une assignation ou une ordonnance du tribunal) ;</li>
                <li>Appliquer nos conditions dutilisation ;</li>
                <li>Coopérer avec les forces de lordre concernant une conduite ou une activité qui, selon nous, peut raisonnablement et de bonne foi, violer la loi fédérale, étatique ou locale ; ou</li>
                <li>Exercer ou défendre des actions en justice, protéger contre toute atteinte à nos droits, à nos biens, à nos intérêts ou à notre sécurité ou à vos droits, à vos biens, à vos intérêts ou à votre sécurité, à celle de tiers ou du public, comme lexige ou le permet la loi.</ li>
                </ul>
                
                <p>D. Dans le cadre dune vente ou dune fusion</p>
                
                <p>Nous pouvons partager vos informations personnelles lors de négociations ou en relation avec un changement de contrôle de lentreprise tel quune restructuration, une fusion ou la vente de nos actifs.</p>
                
                <p>E. Selon vos instructions ultérieures</p>
                
                <p>Avec votre autorisation ou selon vos instructions, nous pouvons divulguer vos informations personnelles pour interagir avec un tiers ou à dautres fins.</p>
                
                <p>Comment nous stockons et protégeons vos informations</p>
                
                <p>Nous conservons vos informations aussi longtemps que nécessaire pour vous fournir, ainsi quà nos autres utilisateurs, la plateforme Mi Softwares. Cela signifie que nous conservons les informations de votre profil aussi longtemps que vous conservez un compte. Nous conservons les informations transactionnelles telles que les courses et les paiements pendant au moins sept ans pour garantir que nous pouvons exercer des fonctions commerciales légitimes, telles que la comptabilité des obligations fiscales. Si vous demandez la suppression de votre compte, nous supprimerons vos informations comme indiqué dans la section « Suppression de votre compte » ci-dessous. Nous prenons des mesures raisonnables et appropriées conçues pour protéger vos informations personnelles. Mais aucune mesure de sécurité ne peut être efficace à 100 %, et nous ne pouvons garantir la sécurité de vos informations, y compris contre les intrusions non autorisées ou les actes de tiers.</p>
                
                <p>Vos droits et choix concernant vos données</p>
                
                <p>Mi Softwares vous offre des moyens daccéder et de supprimer vos informations personnelles ainsi que dexercer dautres droits en matière de données qui vous donnent un certain contrôle sur vos informations personnelles.</p>
                
                <p>A. Tous les utilisateurs</p>
                
                <p>Abonnements par e-mail. Vous pouvez toujours vous désinscrire de nos e-mails commerciaux ou promotionnels en cliquant sur se désinscrire dans ces messages. Nous vous enverrons toujours des e-mails transactionnels et relationnels concernant votre utilisation de la plateforme Mi Softwares.<br />
                <br />
                <strong>Messages texte.</strong> Vous pouvez refuser de recevoir des SMS commerciaux ou promotionnels. Vous pouvez également refuser de recevoir tous les SMS des logiciels Mi (y compris les messages transactionnels ou relationnels. Notez que le fait de refuser de recevoir tous les SMS peut avoir un impact sur votre utilisation de la plateforme Mi Softwares. Les conducteurs peuvent également se désinscrire des messages spécifiques au conducteur en envoyant un SMS STOP. en réponse à un SMS du conducteur Pour réactiver les SMS, vous pouvez envoyer un SMS à START en réponse à un SMS de confirmation de désabonnement.<br />
                <br />
                <strong>Notifications push.</strong> Vous pouvez refuser de recevoir des notifications push via les paramètres de votre appareil. Veuillez noter que le fait de ne plus recevoir de notifications push peut avoir un impact sur votre utilisation de la plateforme Mi Softwares (par exemple, recevoir une notification indiquant que votre trajet est arrivé).<br />
                <br />
                <strong>Informations de profil</strong>. Vous pouvez consulter et modifier certaines informations de compte que vous avez choisi dajouter à votre profil en vous connectant aux paramètres de votre compte et à votre profil.<br />
                <br />
                <strong>Informations de localisation.</strong> Vous pouvez empêcher votre appareil de partager des informations de localisation via les paramètres système de votre appareil. Mais si vous le faites, cela pourrait avoir un impact sur la capacité de Mi Softwares à vous fournir notre gamme complète de fonctionnalités et de services.<br />
                <br />
                <strong>Suivi des cookies.</strong> Vous pouvez modifier vos paramètres de cookies sur votre navigateur, mais si vous supprimez ou choisissez de ne pas accepter nos cookies, vous risquez de manquer certaines fonctionnalités de la plateforme Mi Softwares.<br />
                <br />
                <strong>Ne pas suivre.</strong> Votre navigateur peut vous proposer une option « Ne pas suivre », qui vous permet de signaler aux opérateurs de sites Web et dapplications et services Web que vous ne souhaitez pas quils suivent vos activités en ligne. La plate-forme Mi Softwares ne prend actuellement pas en charge les demandes Do Not Track pour le moment.<br />
                <br />
                <strong>Suppression de votre compte.</strong> Si vous souhaitez supprimer votre compte Mi Softwares, veuillez visiter notre page daccueil de confidentialité. Dans certains cas, nous ne pourrons pas supprimer votre compte, par exemple sil y a un problème avec votre compte lié à la confiance, à la sécurité ou à la fraude. Lorsque nous supprimons votre compte, nous pouvons conserver certaines informations à des fins commerciales légitimes ou pour nous conformer à des obligations légales ou réglementaires. Par exemple, nous pouvons conserver vos informations pour résoudre des réclamations dassurance en cours, ou nous pouvons être obligés de conserver vos informations dans le cadre dune réclamation légale en cours. Lorsque nous conservons ces données, nous le faisons de manière à empêcher leur utilisation à dautres fins.<br />
                <br />
                <strong>Droit de savoir.</strong> Vous avez le droit de savoir et de voir quelles données nous avons collectées, notamment :</p>
                
                <ul>
                <li>Les catégories dinformations personnelles que nous avons collectées à votre sujet ;</li>
                <li>Les catégories de sources à partir desquelles les informations personnelles sont collectées ;</li>
                <li>La finalité commerciale de la collecte de vos informations personnelles ;</li>
                <li>Les catégories de tiers avec lesquels nous avons partagé vos informations personnelles ; et</li>
                <li>Les informations personnelles spécifiques que nous avons collectées à votre sujet.</li>
                </ul>
                
                <p><strong>Droit de suppression.</strong> Vous avez le droit de demander que nous supprimions les informations personnelles que nous avons collectées auprès de vous (et dordonner à nos prestataires de services de faire de même). Il existe cependant un certain nombre dexceptions, qui incluent, sans sy limiter, lorsque les informations sont nécessaires pour nous ou un tiers pour effectuer lune des opérations suivantes :</p>
                
                <ul>
                <li>Terminez votre transaction ;</li>
                <li>Vous fournir un bien ou un service ;</li>
                <li>Exécuter un contrat entre nous et vous ;</li>
                <li>Protégez votre sécurité et poursuivez les responsables de violations ;</li>
                <li>Réparer notre système en cas de bug ;</li>
                <li>Protéger vos droits à la liberté dexpression ou ceux des autres utilisateurs ;</li>
                <li>Participer à des recherches scientifiques, historiques ou statistiques publiques ou évaluées par des pairs dans lintérêt public et dans le respect de toutes les autres lois applicables en matière déthique et de confidentialité ;</li>
                <li>Se conformer à une obligation légale ; ou</li>
                <li>Effectuer dautres utilisations internes et licites des informations, compatibles avec le contexte dans lequel vous les avez fournies.</li>
                </ul>
                
                <p><strong>Autres droits.</strong> Vous pouvez demander certaines informations sur notre divulgation dinformations personnelles à des tiers à des fins de marketing direct au cours de lannée civile précédente. Cette demande est gratuite et peut être effectuée une fois par an. Vous avez également le droit de ne pas être victime de discrimination pour lexercice de lun des droits énumérés ci-dessus.<br />
                <br />
                <strong>Site Web :</strong> Vous pouvez visiter notre page daccueil relative à la confidentialité pour vous authentifier et exercer vos droits via notre site Web.<br />
                <br />
                <strong>Formulaire Web par e-mail :</strong> Vous pouvez nous écrire pour exercer vos droits. Pour répondre à certains droits, nous devrons vérifier votre demande soit en vous demandant de vous connecter et dauthentifier votre compte, soit de vérifier votre identité en fournissant des informations sur vous-même ou sur votre compte. Les agents autorisés peuvent faire une demande en votre nom si vous leur avez donné une procuration légale ou si nous recevons une preuve dautorisation signée, une vérification de votre identité et une confirmation que vous avez donné à lagent lautorisation de soumettre la demande. Délai et format de réponse. Notre objectif est de répondre à une demande d’accès ou de suppression d’un consommateur dans les 45 jours suivant la réception de cette demande. Si nous avons besoin de plus de temps, nous vous informerons par écrit du motif et de la période de prolongation.</p>
                
                <p>Données sur les enfants</p>
                
                <p>Mi Softwares nest pas destiné aux enfants et nous ne collectons pas sciemment dinformations personnelles auprès denfants de moins de 13 ans. Si nous découvrons quun enfant de moins de 13 ans nous a fourni des informations personnelles, nous prendrons des mesures pour les supprimer. information. Si vous pensez quun enfant de moins de 13 ans nous a fourni des informations personnelles, veuillez nous contacter</p>
                
                <p>Liens vers des sites Web tiers</p>
                
                <p>La plateforme Mi Softwares peut contenir des liens vers des sites Web tiers. Ces sites Web peuvent avoir des politiques de confidentialité différentes des nôtres. Nous ne sommes pas responsables de ces sites Web et nous vous recommandons de consulter leurs politiques. Veuillez contacter directement ces sites Web si vous avez des questions concernant leurs politiques de confidentialité.</p>
                
                <p>Modifications apportées à cette politique de confidentialité</p>
                
                <p>Nous pouvons mettre à jour cette politique de temps en temps à mesure que la plateforme Mi Softwares change et que la loi sur la confidentialité évolue. Si nous le mettons à jour, nous le ferons en ligne, et si nous apportons des modifications importantes, nous vous en informerons via la plateforme Mi Softwares ou par une autre méthode de communication comme le courrier électronique. Lorsque vous utilisez Mi Softwares, vous acceptez les conditions les plus récentes de cette politique.</p>
                
                <p>Contactez-nous</p>
                
                <p>Si vous avez des questions ou des préoccupations concernant votre vie privée ou quoi que ce soit dans cette politique, y compris si vous avez besoin daccéder à cette politique dans un format alternatif, nous vous encourageons à nous contacter.</p>',
            'terms_title' => 'Conditions générales',
            'terms' => '<h2><strong>Conditions générales</strong></h2>

                <p>CONTRAT DE LICENCE UTILISATEUR FINAL</p>

                <p>Dernière mise à jour le 16 mai 2021</p>

                <p>Mi Softwares,LLC vous est concédé sous licence (utilisateur final) par Mi Softwares, LLC, situé au 6255 Towncenter Drive Ste 819, Clemmons, Caroline du Nord 27012, États-Unis (ci-après : concédant de licence), pour une utilisation uniquement selon les termes du présent Contrat de Licence.<br />
                <br />
                En téléchargeant lapplication depuis lAppStore dApple et Google Play, ainsi que toute mise à jour de celle-ci (comme le permet le présent contrat de licence), vous indiquez que vous acceptez dêtre lié par tous les termes et conditions du présent contrat de licence et que vous acceptez ce Contrat de licence.<br />
                <br />
                Les parties au présent Contrat de licence reconnaissent quApple et/ou Google Play ne sont pas partie au présent Contrat de licence et ne sont liés par aucune disposition ou obligation concernant lApplication, telles que la garantie, la responsabilité, la maintenance et le support de celle-ci. Mi Softwares, LLC, et non Apple ou Google Play, est seul responsable de lapplication sous licence et de son contenu.<br />
                <br />
                Le présent Contrat de licence ne peut pas prévoir de règles dutilisation de lApplication qui seraient en conflit avec les dernières Conditions dutilisation de lApp Store. Mi Softwares, LLC reconnaît avoir eu la possibilité de consulter lesdites conditions et le présent accord de licence nest pas en conflit avec elles.<br />
                <br />
                Tous les droits qui ne vous sont pas expressément accordés sont réservés.</p>

                <p>1. LA DEMANDE</p>

                <p>Mi Softwares (ci-après : Application) est un logiciel qui constitue une plate-forme de covoiturage - et personnalisé pour les appareils mobiles Apple et Android. Il est utilisé pour connecter les coureurs aux conducteurs pour se rendre dun point A à un point B en appuyant simplement sur un bouton.<br />
                <br />
                LApplication nest pas conçue pour se conformer aux réglementations spécifiques à lindustrie (Health Insurance Portability and Accountability Act (HIPAA), Federal Information Security Management Act (FISMA), etc.), donc si vos interactions sont soumises à de telles lois, vous ne pouvez pas utilisez cette application. Vous ne pouvez pas utiliser lApplication dune manière qui violerait la loi Gramm-Leach-Bliley (GLBA).</p>

                <p>2. PORTÉE DE LA LICENCE</p>

                <p>2.1 Vous disposez dune licence non transférable, non exclusive et ne pouvant faire lobjet dune sous-licence pour installer et utiliser lapplication sous licence sur tout produit de marque Apple ou Google que vous (utilisateur final) possédez ou contrôlez et comme autorisé par le Règles dutilisation énoncées dans cette section et dans les conditions dutilisation de lApp Store, à lexception du fait que cette application sous licence peut être consultée et utilisée par dautres comptes associés à vous (utilisateur final, lacheteur) via le partage familial ou lachat en volume.<br />
                <br />
                2.2 Cette licence régira également toutes les mises à jour de lApplication fournies par le Concédant qui remplacent, réparent et/ou complètent la première Application, à moins quune licence distincte ne soit fournie pour une telle mise à jour, auquel cas les termes de cette nouvelle licence prévaudront.<br />
                <br />
                2.3 Vous ne pouvez pas partager ou mettre lApplication à la disposition de tiers (sauf dans la mesure permise par les Conditions générales dApple et avec le consentement écrit préalable de Mi Softwares, LLC), vendre, louer, prêter ou redistribuer de toute autre manière lApplication. <br />
                <br />
                2.4 Vous ne pouvez pas effectuer dingénierie inverse, traduire, désassembler, intégrer, décompiler, intégrer, supprimer, modifier, combiner, créer des œuvres dérivées ou des mises à jour, adapter ou tenter de dériver le code source de lApplication, ou toute partie de celle-ci (sauf avec consentement écrit préalable de Mi Softwares, LLC).<br />
                <br />
                2.5 Vous ne pouvez pas copier (sauf lorsque cela est expressément autorisé par cette licence et les règles dutilisation) ou modifier lapplication ou des parties de celle-ci. Vous pouvez créer et stocker des copies uniquement sur les appareils que vous possédez ou contrôlez à des fins de sauvegarde conformément aux termes de cette licence, aux conditions de service de lApp Store et à tous les autres termes et conditions applicables à lappareil ou au logiciel utilisé. Vous ne pouvez supprimer aucun avis de propriété intellectuelle. Vous reconnaissez quaucun tiers non autorisé ne peut avoir accès à ces copies à tout moment. <Br />
                <br />
                2.6 Les violations des obligations mentionnées ci-dessus, ainsi que la tentative dune telle violation, peuvent être soumises à des poursuites et à des dommages. <Br />
                <br />
                2.7 Licensor se réserve le droit de modifier les termes et conditions de licence. <Br />
                <br />
                2.8 Rien dans cette licence ne doit être interprété pour restreindre les termes tiers. Lorsque vous utilisez lapplication, vous devez vous assurer que vous respectez les termes et conditions tiers applicables. </p>

                <p>3. Exigences techniques </p>

                <p> 3.1 Le licence tente de maintenir lapplication à jour afin quil se conforme aux versions modifiées / nouvelles du micrologiciel et du nouveau matériel. Vous navez pas le droit de réclamer une telle mise à jour. <Br />
                <br />
                3.2 Vous reconnaissez quil est de votre responsabilité de confirmer et de déterminer que le périphérique de lutilisateur final de lapplication sur lequel vous avez lintention dutiliser lapplication satisfait les spécifications techniques mentionnées ci-dessus. <Br />
                <br />
                3.3 Le licence se réserve le droit de modifier les spécifications techniques telles quelle le juge appropriée à tout moment. </p>

                <p>4. Maintenance et support </p>

                <p> 4.1 Le concédent est seul responsable de fournir des services de maintenance et de support pour cette application sous licence. Vous pouvez atteindre le concédant de licence à ladresse e-mail répertoriée dans lApp Store ou Google Play Présentation de cette application sous licence. <Br />
                <br />
                4.2 MI Softwares, LLC et lutilisateur final reconnaissent quApple et / Google Play nont aucune obligation de fournir des services de maintenance et de soutien en ce qui concerne lapplication sous licence. </p>

                <p>5. Utilisation des données </p>

                <p> Vous reconnaissez que le concédant de licence pourra accéder et ajuster votre contenu de demande de licence téléchargé et vos informations personnelles, et que lutilisation par le concédant de ces documents et informations est soumise à vos accords juridiques avec la politique de confidentialité du concédant et du concédant: http: // www.mi Softwares.us/privacy. </p>

                <p>6. Contributions générées par lutilisateur </p>

                <p> Lapplication peut vous inviter à discuter, à contribuer ou à participer à des blogs, des babillards électroniques, des forums en ligne et dautres fonctionnalités, et peut vous offrir la possibilité de créer, soumettre, publier, afficher, transmettre, exécuter, publier , distribuer ou diffuser du contenu et des matériaux à nous ou dans lapplication, y compris, mais sans sy limiter, le texte, les écrits, la vidéo, laudio, les photographies, les graphiques, les commentaires, les suggestions ou les informations personnelles ou dautres documents (collectivement, "contributions"). Les contributions peuvent être visibles par dautres utilisateurs de lapplication et via des sites Web ou des applications tiers. En tant que tels, toute contribution que vous transmettez peut être traitée comme non confidentielle et non propriétaire. Lorsque vous créez ou mettez à disposition des contributions, vous représentez et garantissez ainsi que: <Br />
                <br />
                1. La création, la distribution, la transmission, laffichage publique ou la performance, et laccès, le téléchargement ou la copie de vos contributions nenfreindront pas et ne violeront pas les droits de propriété, y compris mais sans sy limiter ou droits moraux de tout tiers. <Br />
                <br />
                2. Vous êtes le créateur et le propriétaire de ou avez les licences, droits, consentements, versions et autorisations nécessaires à utiliser et à nous autoriser, lapplication et dautres utilisateurs de lapplication pour utiliser vos contributions de toute manière envisagée par lapplication et ces termes dutilisation. <Br />
                <br />
                3. Vous avez le consentement écrit, la libération et / ou lautorisation de chaque personne individuelle identifiable dans vos contributions pour utiliser le nom ou la ressemblance ou chaque personne identifiable de ces personnes identifiables pour permettre linclusion et lutilisation de vos contributions de quelque manière que ce soit envisagée par lapplication et ces conditions dutilisation. <Br />
                <br />
                4. Vos contributions ne sont pas fausses, inexactes ou trompeuses. <Br />
                <br />
                5. Vos contributions ne sont pas une publicité non sollicitée ou non autorisée, du matériel promotionnel, des schémas pyramidaux, des lettres de chaîne, du spam, des envois de masse ou dautres formes de sollicitation. <Br />
                <br />
                6. Vos contributions ne sont pas obscènes, obscènes, lascives, sales, violentes, harcelantes, diffamées, calomnieuses ou autrement répréhensibles (telles que déterminées par nous). <Br />
                <br />
                7. Vos contributions ne ridiculisent, ne se moquent pas, ne dénigrent pas, ne sont pas en train de vous en abuser. <Br />
                <br />
                8. Vos contributions ne sont pas utilisées pour harceler ou menacer (au sens juridique de ces termes) aucune autre personne et promouvoir la violence contre une personne ou une classe de personnes. <Br />
                <br />
                9. Vos contributions ne violent aucune loi, réglementation ou règle applicable. <Br />
                <br />
                10. Vos contributions ne violent pas les droits de confidentialité ou de publicité dun tiers. <Br />
                <br />
                11. Vos contributions ne contiennent aucun matériel qui sollicite des informations personnelles de toute personne de moins de 18 ans ou exploite les personnes de moins de 18 ans de manière sexuelle ou violente. <Br />
                <br />
                12. Vos contributions ne violent aucune loi applicable concernant la pornographie juvénile, ou autrement destinée à protéger la santé ou le bien-être des mineurs. <Br />
                <br />
                13. Vos contributions nincluent pas de commentaires offensants liés à la race, à lorigine nationale, au sexe, aux préférences sexuelles ou au handicap physique. <Br />
                <br />
                14. Vos contributions ne violent pas autrement, ni ne sont liées à des éléments qui violent, toute disposition des présentes conditions dutilisation, ni toute loi ou réglementation applicable. <Br />
                <br />
                Toute utilisation de la demande en violation de ce qui précède viole ces conditions dutilisation et peut entraîner, entre autres, la résiliation ou la suspension de vos droits dutiliser la demande. </p>

                <p>7. Licence de contribution </p>

                <p> En publiant vos contributions à toute partie de la demande ou en rendant les contributions accessibles à la demande en reliant votre compte à partir de la demande à lun de vos comptes de réseautage social, vous accordez automatiquement, et vous représentez et garantissez que vous avez le droit de Accordez-nous, un peu restreint, illimité, irrévocable, perpétuel, non exclusif, transférable, libre de droits, entièrement payé, à droite mondiale et licence pour héberger, utiliser la copie, reproduire, divulguer, vendre, revendre, publier, une plâtre large , retiser, archiver, magasin, cache, afficher publiquement, reformater, traduire, transmettre, extraire (en tout ou en partie), et distribuer de telles contributions (y compris, sans limitation, votre image et votre voix) à quelque fin que ce soit, la publicité commerciale ou autrement , et préparer des œuvres dérivées ou incorporer dans dautres œuvres, telles que les contributions, et accorder et autoriser les subli que ce qui précède. Lutilisation et la distribution peuvent se produire dans tous les formats multimédias et à travers tous les canaux multimédias. <Br />
                <br />
                Cette licence sappliquera à tous les formulaires, médias ou technologies désormais connus ou par la suite, et comprend notre utilisation de votre nom, nom de lentreprise et nom de franchise, selon lesquels vous êtes applicable, et lune des marques, des marques, des noms commerciaux, des logos, et les images personnelles et commerciales que vous fournissez. Vous renoncez à tous les droits moraux dans vos contributions, et vous garantissez que les droits moraux nont pas été autrement affirmés dans vos contributions. <Br />
                <br />
                Nous naffirmons aucune propriété sur vos contributions. Vous conservez la pleine propriété de toutes vos contributions et de tous les droits de propriété intellectuelle ou dautres droits de propriété associés à vos contributions. Nous ne sommes pas responsables des déclarations ou des représentations de vos contributions fournies par vous dans nimporte quel domaine de lapplication. Vous êtes seul responsable de vos contributions à la demande et vous acceptez expressément de nous exonérer de toutes les responsabilités et de vous abstenir de toute action en justice contre nous concernant vos contributions. <Br />
                <br />
                Nous avons le droit, à notre seule et absolue discrétion, (1) de modifier, de refuser ou de modifier autrement les contributions; (2) pour recommandé toute contribution pour les placer dans des endroits plus appropriés dans lapplication; et (3) pré-écran ou supprimer toute contribution à tout moment et pour quelque raison que ce soit, sans préavis. Nous navons aucune obligation de surveiller vos contributions. </p>

                <p>8. Responsabilité </p>

                <P> 8.1 La responsabilité du concédant en cas de violation des obligations et du délit est limitée à lintention et à la négligence grave. Ce nest quen cas de violation des tâches contractuelles essentielles (obligations cardinales), le concédant est également responsable en cas dune légère négligence. Dans tous les cas, la responsabilité est limitée aux dommages prévisibles et contractuellement typiques. La limitation mentionnée ci-dessus ne sapplique pas aux blessures à la vie, aux membres ou à la santé. <Br />
                <br />
                8.2 Le concédant de licence ne prend aucune responsabilité ou responsabilité des dommages causés en raison dune violation des fonctions conformément à larticle 2 du présent accord. Pour éviter la perte de données, vous devez utiliser les fonctions de sauvegarde de lapplication dans la mesure autorisée par les termes et conditions dutilisation tierces applicables. Vous savez quen cas de modifications ou de manipulations de la demande, vous naurez pas accès à une demande agréée. </p>

                <p>9. Garantie </p>

                <p> 9.1 Le concédant garantit que lapplication est gratuite de logiciels espions, de chevaux de Troie, de virus ou de tout autre logiciel malveillant au moment de votre téléchargement. Le concédant garantit que lapplication fonctionne comme décrit dans la documentation utilisateur. <Br />
                <br />
                9.2 Aucune garantie nest fournie pour lapplication qui nest pas exécutable sur lappareil, qui a été modifiée sans autorisation, gérée de manière inappropriée ou culpabilité, combinée ou installée avec un matériel ou un logiciel inapproprié, utilisé avec des accessoires inappropriés, indépendamment de vous-même ou par des tiers, ou sil y a dautres raisons en dehors des logiciels MI, la sphère dinfluence de LLC qui affecte lexécutabilité de lapplication. <Br />
                <br />
                9.3 Vous devez inspecter lapplication immédiatement après son installation et informer Mi Softwares, LLC sur les problèmes découverts sans délai par e-mail fourni dans les réclamations du produit. Le rapport des défauts sera pris en considération et a en outre étudié sil a été envoyé par la poste dans une période de quatre-vingt-dix (90) jours après la découverte. <Br />
                <br />
                9.4 Si nous confirmons que lapplication est défectueuse, MI Softwares, LLC se réserve un choix pour remédier à la situation en résolvant le défaut ou la livraison de remplacement. <Br />
                <br />
                9.5 En cas de non-non-conformité de la demande à toute garantie applicable, vous pouvez en informer lapplication-opérateur de lapplication et votre prix dachat de demande vous sera remboursé. Dans la mesure maximale autorisée par la loi applicable, lopérateur dapplication-stockage naura aucune autre obligation de garantie en ce qui concerne lapplication et toute autre perte, réclamation, dommages-intérêts, passifs, dépenses et coûts attribuables à toute négligence pour respecter tout Garantie. <Br />
                <br />
                9.6 Si lutilisateur est un entrepreneur, toute réclamation basée sur les défauts expire après une période de limitation statutaire sélevant à douze (12) mois après la mise à la disposition de lutilisateur de lapplication. Les périodes statutaires de limitation données par la loi sappliquent aux utilisateurs qui sont consommateurs. </p>

                <p>10. Réclamations du produit </p>

                <p> MI Softwares, LLC et lutilisateur final reconnaissent que Mi Softwares, LLC, et non Apple, est chargé de répondre à toute réclamation de lutilisateur final ou de tout tiers relatif à la demande agréée ou à la possession de lutilisateur final et / ou utiliser cette application sous licence, y compris, mais sans sy limiter: <r />
                <br />
                (i) Réclamations de responsabilité des produits; <Br />
                <br />
                (ii) toute réclamation selon laquelle la demande agréée ne se conforme pas à toute exigence légale ou réglementaire applicable; et <br />
                <br />
                (iii) les réclamations résultant de la protection des consommateurs, de la vie privée ou de la législation similaire, y compris dans le cadre de lutilisation de votre demande agréée. </p>

                <p>11. Conformité juridique </p>

                <p> Vous représentez et garantissez que vous nêtes pas situé dans un pays soumis à un embargo du gouvernement américain, ou qui a été désigné par le gouvernement américain comme un pays de "soutien terroriste"; et que vous nêtes répertorié sur aucune liste du gouvernement américain des parties interdites ou restreintes. </p>

                <p>12. Coordonnées </p>

                <p> Pour les enquêtes générales, les plaintes, les questions ou les réclamations concernant la demande agréée, veuillez contacter: <r />>
                <br />
                <strong> MI Softwares, LLC <Br />
                6255 TownCenter Drive Ste 819 <Br />
                Clemmons, NC 27012 <Br />
                États-Unis <Br />
                support @ mi softwares.us </strong> </p>

                <p> 13. Terminaison </p>

                <p> La licence est valide jusquà ce quelle soit terminée par Mi Softwares, LLC ou par vous. Vos droits en vertu de cette licence se termineront automatiquement et sans préavis de Mi Softwares, LLC si vous ne respectez aucun terme de cette licence. À la fin de la licence, vous devez arrêter toute utilisation de la demande et détruire toutes les copies, complètes ou partielles, de la demande. </p>

                <p> 14. Conditions des accords et bénéficiaires tiers </p>

                <p> MI Softwares, LLC représente et justifie que Mi Softwares, LLC se conformera aux conditions de laccord tierces applicables lors de lutilisation de lapplication sous licence. <Br />
                <br />
                Conformément à la section 9 des "Instructions pour les conditions minimales du contrat de licence de lutilisateur final du développeur", les filiales dApple et dApple seront des bénéficiaires tiers du présent contrat de licence utilisateur final et - lors de votre acceptation des termes et conditions de la présente licence Accord, Apple aura le droit (et sera réputé avoir accepté le droit) pour appliquer ce contrat de licence dutilisateur final contre vous en tant que bénéficiaire tiers de celui-ci. </p>

                <p> 15. Droits de propriété intellectuelle </p>

                <P> MI Softwares, LLC et lutilisateur final reconnaissent que, en cas de réclamation de tiers que la demande agréée ou la possession et lutilisation de cette demande de licence infligées aux droits de propriété intellectuelle du tiers, MI, MI Softwares, LLC, et non Apple, sera seul responsable de lenquête, de la défense, du règlement et de la libération ou de toutes ces réclamations dinfraction de propriété intellectuelle. </p>

                <p> 16. Loi applicable </p>

                <p> Cet accord de licence est régi par les lois de lÉtat de Caroline du Nord à lexclusion de ses règles de conflits de droit. </p>

                <p> 17. Divers </p>

                <p> 17.1 Si lun des termes du présent accord doit être ou devenir invalide, la validité des dispositions restantes ne sera pas affectée. Les termes non valides seront remplacés par des valides formulées dune manière qui atteindra lobjectif principal. <Br />
                <br />
                17.2 Les accords de garantie, les modifications et les modifications ne sont valables que sils sont établis par écrit. La clause précédente ne peut être annulée quen écriture. </p>' ,
            'compliance_title' => 'conformité',
            'compliance' => '<h3> <strong> Opportunité demploi égal et politique de non-discrimination </strong> </h3>

                <h3> i. Présentation et portée </h3>

                <p> Mi Softwares, LLC de 6255 TownCenter Drive Ste 819, Clemmons, Caroline du Nord 27012, a établi une politique de non-discrimination et dégalité des chances demploi ("EEO"). Cette politique EEO sapplique à tous les aspects de la relation entre les Softwares, LLC et ses employés, y compris, mais sans sy limiter, lemploi, le recrutement, les publicités pour lemploi, lembauche et le licenciement, la rémunération, la mission, la classification des employés, la cessation, la mise à niveau, Promotions, transfert, formation, conditions de travail, salaire et administration salariale, et avantages sociaux et application des politiques. Ces politiques sappliquent aux entrepreneurs indépendants, aux employés temporaires, à tout le personnel travaillant sur les lieux et à toute autre personne ou entreprise qui fait des affaires pour ou avec Mi Softwares, LLC. Tout utilisateur qui a violé cette interdiction perdra laccès à la plate-forme MI Softwares, LLC. Les lois applicables dans certaines juridictions peuvent exiger et / ou permettre la prestation de services par et pour le bénéfice dune catégorie spécifique de personnes. Dans de telles juridictions, les services fournis conformes à ces lois et les conditions applicables pertinentes sont autorisées en vertu de la présente politique. </p>

                <h3> ii. Politiques </H3>

                <p>1. Discrimination. </p>

                <P> MI Softwares, LLC ne tolérera en aucune circonstance, sans exception, une forme de discrimination fondée sur la race, la croyance, la religion, la couleur, lâge, linvalidité, la grossesse, létat matrimonial, létat parental, lorientation sexuelle, lexpression des sexes, Identité de genre, statut de vétéran, statut militaire, statut de victime de violence domestique, origine nationale, affiliation politique, sexe, caractéristiques génétiques prédisposantes ou emplacement géographique et tout autre statut protégé par la loi. Cette liste nest pas exhaustive. Pour les personnes qualifiées handicapées, MI Softwares, LLC fera tout leur possible pour fournir des logements en milieu de travail raisonnables conformes aux lois applicables. </p>

                <p> La discrimination dans la prestation de services de transport est strictement interdite </p>

                <p> Les moteurs et les employés associés sont tenus de connaître les interdictions de non-discrimination. MI Softwares, LLC ne tolérera pas les adaptations publiques, qui comprend les services de taxigs pratiques discriminatoires illégales pour nier, directement ou indirectement, toute personne la jouissance complète et égale des biens, services, installations, privilèges, avantages et adaptations de tout endroit des logements publics (y compris les services de taxi) entièrement ou partiellement pour une raison discriminatoire basée sur le lieu de résidence ou dentreprise. </p>

                <p> Conduite discriminatoire interdite: </p>

                <p> mi Softwares, LLC reconnaît que les conducteurs associés ne doivent jamais discriminer certains clients en ne les ramassant pas, ne les prenant pas là où ils souhaitent aller ou en les traitant avec moins de respect en fonction des caractéristiques ou des traits protégés énumérés ci-dessus. Des exemples spécifiques de conduite discriminatoire, incluent les éléments suivants: <r />
                <br />
                Ne pas ramasser un passager sur la base dune caractéristique ou dun trait protégé, notamment en ne prenant pas un passager avec un animal dassistance · demandant quun passager sorte dun taxi sur la base dune caractéristique ou dun trait protégée · en utilisant un langage dérogatoire ou harcelant sur la base dune caractéristique ou dun trait protégé · refuser un ramassage dans une zone géographique spécifique. </p>

                <p> Discrimination géographique: </p>

                <P> MI Softwares, LLC ne tolère pas la discrimination géographique et reconnaît à quel point il est important damener le client à la destination demandée sans discriminer ce client en fonction de lendroit où il souhaite aller. Tous les moteurs, employés, gestionnaires, parties prenantes et agents associés de MI Softwares, LLC se conformeront à ces politiques anti-discrimination. Dans certains cas, les lois et réglementations locales peuvent fournir des protections plus importantes que celles décrites dans cette politique. </p>

                <p>2. Harcèlement </p>

                <p> Mi Softwares, LLC sengage à fournir un environnement de travail sans harcèlement. Tout comportement indésirable et offensant pour le destinataire, ce qui crée un environnement de travail intimidant, hostile ou humiliant pour cette personne viole la politique de MI Softwares, LLC. Le harcèlement peut se produire entre les membres du sexe opposé ou le même sexe. Le harcèlement, verbal ou non verbal, explicite ou implicite, basé sur le sexe, la race, lethnicité, lorigine nationale, lâge, lâge, la religion ou toute autre caractéristique légalement protégée ne seront pas tolérés. Tous les employés, y compris les superviseurs, dautres membres du personnel de gestion et les entrepreneurs indépendants, sont tenus de respecter cette politique. Aucune personne ne sera affectée négativement dans lemploi avec MI Softwares, LLC en raison de la portion de plaintes de harcèlement. </p>

                <p>3. Harcèlement sexuel </p>

                <p> Les avancées sexuelles non les bienvenues, les demandes de faveurs sexuelles et dautres conduites verbales ou physiques de nature sexuelle constituent du harcèlement lorsque (1) la soumission à une telle conduite est faite explicitement ou implicitement un terme ou une condition demploi; (2) la soumission ou le rejet ou le rejet dune telle conduite par un individu est utilisée comme base pour les décisions demploi, la promotion, le transfert, la sélection de formation, les évaluations du rendement, les avantages sociaux ou dautres termes et conditions demploi; ou (3) une telle conduite a le but ou leffet de la création dun environnement de travail intimidant, hostile ou offensif ou interfère considérablement avec le rendement du travail dun employé. MI Softwares, LLC interdit une conduite inappropriée de nature sexuelle au travail, sur les entreprises de lentreprise ou lors dévénements parrainés par lentreprise, y compris les suivants: commentaires, blagues, langage dégradant, objets sexuellement suggestifs, livres ou toute forme de média électronique ou dans formulaire dimpression. Le harcèlement sexuel est interdit de savoir sil se situe entre des membres du sexe opposé ou des membres du même sexe. </p>

                <p>4. Déclaration sur laction positive </p>

                <p> Un programme daction positive a été développé lorsque Mi Softwares, LLC cherche à augmenter la représentation et la participation des minorités </p>

                <p>5. Discrimination et harcèlement de signalement </p>

                <p> Si un employé estime quil a été harcelé comme décrit dans cette politique, il doit immédiatement déposer des griefs avec: Grieval Department, 6255 TownCenter Drive, Ste 819, Clemmons NC 27012, ou par e-mail à Compliance @ Mi Softwares. nous. Une fois laffaire signalée, elle sera rapidement étudiée et toute mesure corrective sera prise lorsquelle sera jugée appropriée. Toutes les plaintes ou harcèlement illégal en vertu de cette politique ou autrement seront traités de manière aussi confidentielle que possible. Les rapports en temps opportun sont encouragés à empêcher la réapparition ou à répondre autrement au comportement qui viole cette politique ou la loi. Les retards dans la déclaration dune plainte peuvent limiter le type defficacité dune réponse par Mi Softwares, LLC. La procédure de signalement des incidents de comportement discriminatoire ou de harcèlement nest pas destiné à empêcher le droit de tout employé de demander un recours en vertu de la loi de lÉtat ou de la loi disponible en rapportant immédiatement laffaire à lÉtat ou à lagence fédérale appropriée. </p>

                <p>6. Représailles </p>

                <p> représailles contre toute personne associée à Mi Softwares, LLC qui rapporte des cas de harcèlement - quil soit directement ou indirectement impliquée - est en violation des politiques de MI Softwares, LLC. Tous les incidents signalés sont supposés être faits de bonne foi. Toutes les allégations qui sont prouvées fausses seront traitées comme une affaire sérieuse. </p>

                <p>7. Mesures disciplinaires de harcèlement </p>

                <p> Tout employé adoptant des comportements qui viole cette politique sera soumis à des mesures disciplinaires, y compris la résiliation possible de lemploi, si une loi réelle a été violée ou non. </p>

                <p>8. Remèdes </p>

                <p> Remèdes pour tout cas de discrimination en matière demploi vérifiée, quil soit causé intentionnellement ou par des actions qui ont un effet discriminatoire, peuvent inclure le rémunération en arrière, lembauche, la promotion, la réintégration, le salaire avant, lhébergement raisonnable ou dautres actions jugées appropriées par les logiciels de lIM, SARL. Les recours peuvent également inclure le paiement des honoraires davocat, des frais de témoin expert, des frais de justice et dautres frais juridiques applicables. </p>

                <p>9. Mise en œuvre de la politique </p>

                Le PDG de MI Softwares, Lynn Graham, soutient pleinement la mise en œuvre de cette politique à compter du 19 avril 2021. </p>' ,
            'dmv_title' => 'dmv check',
            'dmv' => '<h2> <strong> dmv chèque et vérification des antécédents consentement </strong> </h2>

                <p> </p>

                <p> Consentement pour demander le dossier de conduite </p>

                <p> Je comprends que Mi Softwares, LLC. («Société») utilisera Checkr., («Checkr, Inc.») pour obtenir un enregistrement des véhicules à moteur dans le cadre du processus de candidature pour être un conducteur sur la plate-forme MI Softwares (un «conducteur»). Je comprends également que si elle est acceptée en tant que conducteur, dans la mesure permise par la loi, la société peut obtenir dautres rapports de Checkr Inc. afin de mettre à jour, renouveler ou étendre mon statut de conducteur. Je donne par la présente la permission aux logiciels MI dobtenir mon dossier de conduite dÉtat (également connu sous le nom de dossier de véhicule à moteur ou MVR) conformément à la loi fédérale sur la protection de la vie privée («DPPA») et à la loi de lÉtat applicable. Je reconnais et comprends que mon dossier de conduite est un rapport de consommation qui contient des informations sur les enregistrements publics. Jautorise, sans réservation de partie ou dagence contactée par la société ou Checkr Inc. pour fournir à lentreprise une copie de mon dossier de conduite dÉtat. Cette autorisation restera dans le dossier par la Société pour la durée de mon temps en tant que chauffeur et servira dautorisation continue à lentreprise pour obtenir mon dossier de conduite dÉtat à tout moment pendant que je suis un chauffeur. </p>

                <p> Consentement pour demander le rapport des consommateurs ou les informations sur le rapport des consommateurs denquête </p>

                <p> Je comprends que Mi Softwares, LLC. («Company») utilisera Checkr Inc., </p>

                <p> 1 Montgomery St, Ste 2000, San Francisco, CA 94104 </p>

                <p> Pour obtenir un rapport de consommation ou un rapport de consommation dinvestigation dans le cadre du processus de demande, être un pilote sur la plate-forme MI Softwares (un «conducteur»). Je comprends également que si elle est acceptée en tant que conducteur, dans la mesure permise par la loi, la société peut obtenir dautres rapports de Checkr afin de mettre à jour, de renouveler ou détendre mon statut de conducteur. </p>

                <p> Je comprends que lenquête de CheckR, Inc («Checkr») peut inclure lobtention dinformations concernant mon casier judiciaire, sous réserve de toute limitation imposée par la loi fédérale et étatique applicable. Je comprends que ces informations peuvent être obtenues par contact direct ou indirect avec des agences publiques ou dautres personnes qui peuvent avoir de telles connaissances. </p>

                <p> La nature et la portée de lenquête recherchée comprendront une vérification des antécédents criminels et une trace SSN. </p>

                <p> Je reconnais la réception du résumé ci-joint de mes droits en vertu de la Fair Credit Reporting Act et, comme lexige la loi, tout résumé des droits liés (collectivement «Résumé des droits»). </p>

                <p> Ce consentement naffectera pas ma capacité à remettre en question ou à contester lexactitude des informations contenues dans un rapport. Je comprends que si lentreprise prend la décision conditionnelle de me disqualifier basée sur tout ou en partie sur mon rapport, je recevrai une copie du rapport et une autre copie des résumés des droits, et si je ne suis pas daccord avec lexactitude de la prétendue disqualification Informations Dans le rapport, je dois informer lentreprise dans les cinq jours ouvrables suivant ma réception du rapport que je conteste lexactitude de ces informations avec Checkr. </p>

                <p> Je consentement à cette enquête et autorise la société à obtenir un rapport sur mes antécédents. </p>

                <p> Afin de vérifier mon identité aux fins de la préparation du rapport, je publie volontairement ma date de naissance, le numéro de sécurité sociale et les autres informations et je comprends pleinement que toutes les décisions sont basées sur des raisons non discriminatoires légitimes. </ P >

                <p> Le nom, ladresse et le numéro de téléphone de lunité la plus proche de lagence de rapports de consommation désignée pour gérer les demandes de renseignements concernant le rapport des consommateurs denquête est: </p>

                <p> <strong> Checkr, Inc. <Br />
                1 Montgomery St, Ste 2000, San Francisco, CA 94104 <Br />
                844-824-3257 </strong> <br />
                <br />
                <Strong> California, Maine, Massachusetts, Minnesota, New Jersey & Oklahoma Demandeurs: </strong> Jai le droit de demander une copie de tout rapport obtenu par la société à partir de Checkr en vérifiant la case. (Vérifiez uniquement si vous souhaitez recevoir une copie) </p>

                <p> Les candidats de New York uniquement </p>

                <p> Je reconnais également que j’ai reçu la copie ci-jointe de l’article 23A de la loi sur la correction de New York. Je comprends en outre que je peux demander une copie de tout rapport de consommation denquête en contactant Checkr. Je comprends en outre que je serai conseillé si des chèques supplémentaires sont demandés et ont fourni le nom et ladresse de lagence de rapport de consommation. </p>

                <p> California Demandeurs et résidents </p>

                <p> Si je postule en Californie ou que je réside en Californie, je comprends que jai le droit dinspecter visuellement les fichiers me concernant maintenus par une agence de rapport de consommation denquête pendant les heures normales de bureau et sur un préavis raisonnable. Linspection peut être effectuée en personne et, si japparais en personne et fournisse une identification appropriée; Jai droit à une copie du dossier moyennant des frais de ne pas dépasser les coûts réels de la duplication. Jai le droit dêtre accompagné dune personne de mon choix, qui fournira une identification raisonnable. Linspection peut également être effectuée par courrier certifié si je fais une demande écrite, avec une identification appropriée, pour que des copies soient envoyées à un destinataire spécifié. Je peux également demander un résumé des informations à fournir par téléphone si je fais une demande écrite, avec une identification appropriée pour la divulgation téléphonique, et la charge de péage, le cas échéant, pour lappel téléphonique est prépayé par ou directement facturé. Je comprends en outre que lagence de déclaration des consommateurs dinvestigation doit fournir du personnel formé pour mexpliquer lune des informations me fournies; Je recevrai de lagence de reporting des consommateurs denquête une explication écrite de toute information codée contenue dans les fichiers maintenus sur moi. «Une identification appropriée» telle que utilisée dans ce paragraphe signifie que les informations sont généralement jugées suffisantes pour identifier une personne, y compris des documents tels quun permis de conduire valide, un numéro de compte de sécurité sociale, une carte didentification militaire et des cartes de crédit. Je comprends que je peux accéder au site Web suivant Checkr.com Privacy pour afficher les pratiques de confidentialité de Checkr, y compris les informations concernant la préparation et le traitement de Checkr des rapports de consommation denquête et de conseils quant à la question de savoir si mes informations personnelles seront envoyées en dehors des États-Unis ou de ses territoires .</p>

                <p> Un résumé de vos droits en vertu de la Fair Credit Reporting Act </p>

                <p> La Federal Fair Credit Reporting Act (FCRA) favorise lexactitude, léquité et la confidentialité des informations dans les dossiers des agences de rapport de consommation. Il existe de nombreux types dagences de déclaration des consommateurs, notamment des bureaux de crédit et des agences spécialisées (telles que les agences qui vendent des informations sur les antécédents de rédaction de chèques, les dossiers médicaux et les dossiers de lhistorique de location). Voici un résumé de vos principaux droits en vertu de la FCRA. <strong> Pour plus dinformations, y compris des informations sur les droits supplémentaires, rendez-vous sur www.consumerfinance.gov/Learnmore ou écrivez à: </strong> </p>

                <p> Bureau de protection financière des consommateurs <br />
                1700 G Street NW, Washington, DC 20552 </p>

                <p> </p>

                <ul>
                <li> Vous devez vous dire si des informations dans votre fichier ont été utilisées contre vous. Quiconque utilise un rapport de crédit ou un autre type de rapport de consommation pour refuser votre demande de crédit, dassurance ou demploi - ou pour prendre une autre action défavorable contre vous - doit vous le dire et doit vous donner le nom, ladresse et le numéro de téléphone de lagence qui a fourni les informations. </li>
                <li> Vous avez le droit de savoir ce quil y a dans votre fichier. Vous pouvez demander et obtenir toutes les informations vous concernant dans les fichiers dune agence de rapports de consommation (votre «divulgation de fichiers»). Vous devrez fournir une identification appropriée, qui peut inclure votre numéro de sécurité sociale. Dans de nombreux cas, la divulgation sera gratuite. Vous avez droit à une divulgation de fichiers gratuite si:
                <ol>
                <li> Une personne a pris des mesures défavorables contre vous en raison des informations dans votre rapport de crédit; </li>
                <li> Vous êtes victime dun vol didentité et placez une alerte de fraude dans votre dossier; </li>
                <li> Votre fichier contient des informations inexactes à la suite dune fraude; </li>
                <li> Vous êtes sous aide publique; </li>
                <li> Vous êtes au chômage, mais vous attendez à demander un emploi dans les 60 jours. </li>
                </ol>
                En outre, tous les consommateurs ont droit à une divulgation gratuite tous les 12 mois après la demande de chaque bureau de crédit national et des agences de rapports de consommateurs spécialisées à léchelle nationale. Voir www.consumerfinance.gov/Learnmore pour plus dinformations. </li>
                <li> Vous avez le droit de demander une cote de crédit. Les cotes de crédit sont des résumés numériques de votre solvabilité sur la base des informations des bureaux de crédit. You may request a credit score from consumer reporting agencies that create scores or distribute scores used in residential real property loans, but you will have to pay for it. In some mortgage transactions, you will receive credit score information for free from the mortgage lender.</li>
                <li>You have the right to dispute incomplete or inaccurate information. If you identify information in your file that is incomplete or inaccurate, and report it to the consumer reporting agency, the agency must investigate unless your dispute is frivolous. See www.consumerfinance.gov/learnmore for an explanation of dispute procedures.</li>
                <li>Consumer reporting agencies must correct or delete inaccurate, incomplete, or unverifiable information. Inaccurate, incomplete or unverifiable information must be removed or corrected, usually within 30 days. However, a consumer reporting agency may continue to report information it has verified as accurate.</li>
                <li>Consumer reporting agencies may not report outdated negative information. In most cases, a consumer reporting agency may not report negative information that is more than seven years old, or bankruptcies that are more than 10 years old.</li>
                <li>Access to your file is limited. A consumer reporting agency may provide information about you only to people with a valid need – usually to consider an application with a creditor, insurer, employer, landlord, or other business. The FCRA specifies those with a valid need for access.</li>
                <li>You must give your consent for reports to be provided to employers. A consumer reporting agency may not give out information about you to your employer, or a potential employer, without your written consent given to the employer. Written consent generally is not required in the trucking industry. For more information, go to www.consumerfinance.gov/learnmore</li>
                <li>You may limit “prescreened” offers of credit and insurance you get based on information in your credit report. Unsolicited “prescreened” offers for credit and insurance must include a toll-free phone number you can call if you choose to remove your name and address from the lists these offers are based on. You may opt-out with the nationwide credit bureaus at 1-888-567-8688.</li>
                <li>You may seek damages from violators. If a consumer reporting agency, or, in some cases, a user of consumer reports or a furnisher of information to a consumer reporting agency violates the FCRA, you may be able to sue in state or federal court.</li>
                <li>Identity theft victims and active duty military personnel have additional rights. For more information, visit www.consumerfinance.gov/learnmore.</li>
                </ul>

                <p>States may enforce the FCRA, and many states have their own consumer reporting laws. In some cases, you may have more rights under state law. For more information, contact your state or local consumer protection agency or your state Attorney General. For information about your federal rights, contact:</p>

                <p> </p>

                <table>
                <thead>
                <tr>
                <th>
                <p>Type of business</p>
                </th>
                <th>
                <p>Contact</p>
                </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                <td>1.a. Banks, savings associations, and credit unions with total assets of over $10 billion and their affiliates.</td>
                <td>a. Consumer Financial Protection Bureau 1700 G Street NW, Washington, DC 20552</td>
                </tr>
                <tr>
                <td>1.b. Such affiliates that are not banks, savings associations, or credit unions also should list, in addition to the CFPB:</td>
                <td>b. Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td colspan="2">
                <p>To the extent not included in item 1 above</p>
                </td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>2.a. National banks, federal savings associations, and federal branches and federal agencies of foreign banks</td>
                <td>a. Office of the Comptroller of the Currency Customer Assistance Group 1301 McKinney Street Suite 3450, Houston, TX 77010-9050</td>
                </tr>
                <tr>
                <td>2.b. State member banks, branches and agencies of foreign banks (other than federal branches, federal agencies, and Insured State Branches of Foreign Banks), commercial lending companies owned or controlled by foreign banks, and organizations operating under section 25 or 25A of the Federal Reserve Act</td>
                <td>b. Federal Reserve Consumer Help Center P.O. Box 1200 Minneapolis, MN 55480</td>
                </tr>
                <tr>
                <td>2.c. Nonmember Insured Banks, Insured State Branches of Foreign Banks, and insured state savings associations</td>
                <td>c. FDIC Consumer Response Center 1100 Walnut Street Box #11, Kansas City, MO 64106</td>
                </tr>
                <tr>
                <td>2.d. Federal Credit Unions</td>
                <td>d. National Credit Union Administration Office of Consumer Protection (OCP), Division of Consumer Compliance and Outreach (DCCO) 1775 Duke Street, Alexandria, VA 22314</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>3. Air carriers</td>
                <td>Asst. General Counsel for Aviation Enforcement & Proceedings Aviation Consumer Protection Division Department of Transportation 1200 New Jersey Avenue SE, Washington, DC 20590</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>4. Creditors Subject to Surface Transportation Board</td>
                <td>Office of Proceedings, Surface Transportation Board, Department of Transportation 395 E Street SW, Washington, DC 20423</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>5. Creditors Subject to Packers and Stockyards Act, 1921</td>
                <td>Nearest Packers and Stockyards Administration area supervisor</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>6. Small Business Investment Companies</td>
                <td>Associate Deputy Administrator for Capital Access, United States Small Business Administration 409 Third Street SW 8th Floor, Washington, DC 20416</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>7. Brokers and Dealers</td>
                <td>Securities and Exchange Commission 100 F St NE, Washington, DC 20549</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>8. Federal Land Banks, Federal Land Bank Associations, Federal Intermediate Credit Banks, and Production Credit Associations</td>
                <td>Farm Credit Administration, 1501 Farm Credit Drive, McLean, VA 22102-5090</td>
                </tr>
                </tbody>
                <tbody>
                <tr>
                <td>9. Retailers, Finance Companies, and All Other Creditors Not Listed Above</td>
                <td>FTC Regional Office for region in which the creditor operates or Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                </table>',
            'locale' => 'fr',
            'language' => 'French',
            'direction' => 'ltr',
        ],
         // spanish
         [
            'id' => Str::uuid(),
            'privacy_title' => 'Política de privacidad',
            'privacy' =>'<h2>Política de Privacidad</h2>

                <p>El alcance de esta política</p>
                
                <p>Esta política se aplica a todos los usuarios de Mi Softwares, incluidos los pasajeros y conductores (incluidos los solicitantes de controladores), y a todas las plataformas y servicios de Mi Softwares, incluidas nuestras aplicaciones, sitios web, funciones y otros servicios (colectivamente, la “Plataforma Mi Softwares”). ”). Recuerde que su uso de la plataforma Mi Softwares también está sujeto a nuestros Términos de servicio.</p>
                
                <p>La información que recopilamos</p>
                
                <p>Cuando utiliza la plataforma Mi Softwares, recopilamos la información que usted proporciona, información de uso e información sobre su dispositivo. También recopilamos información sobre usted de otras fuentes, como servicios de terceros y programas opcionales en los que participa, que podemos combinar con otra información que tenemos sobre usted. Estos son los tipos de información que recopilamos sobre usted:</p>
                
                <p>A. Información que usted nos proporciona</p>
                
                <p>Registro de cuenta. Cuando crea una cuenta con Mi Softwares, recopilamos la información que nos proporciona, como su nombre, dirección de correo electrónico, número de teléfono e información de pago. Puede optar por compartir información adicional con nosotros para su perfil de Usuario, como su foto o direcciones guardadas (por ejemplo, casa o trabajo), y configurar otras preferencias (como sus pronombres preferidos).<br />
                <br />
                <strong>Información del conductor.</strong> Si solicita ser conductor, recopilaremos la información que proporcione en su solicitud, incluido su nombre, dirección de correo electrónico, número de teléfono, fecha de nacimiento, foto de perfil, dirección física, identificación gubernamental. número (como el número de seguro social), información de la licencia de conducir, información del vehículo e información del seguro del automóvil. Recopilamos la información de pago que usted nos proporciona, incluidos los números de ruta bancaria y la información fiscal. Dependiendo de dónde desee conducir, también podemos solicitarle información adicional sobre licencias o permisos comerciales u otra información para administrar la conducción y los programas relevantes para esa ubicación. Es posible que necesitemos información adicional suya en algún momento después de que se convierta en Conductor, incluida información para confirmar su identidad (como una foto).<br />
                <br />
                <strong>Información de contacto SOS</strong> Recopilaremos el permiso de la lista de contactos para enumerar los contactos mientras agregamos los contactos sos para la aplicación de usuario y conductor<br />
                <br />
                <strong>Calificaciones y comentarios.</strong> Cuando usted califica y proporciona comentarios sobre los Pasajeros o Conductores, recopilamos toda la información que usted proporciona en sus comentarios.<br />
                <br />
                <strong>Comunicaciones.</strong> Cuando usted se comunica con nosotros o nosotros nos comunicamos con usted, recopilamos cualquier información que usted proporcione, incluido el contenido de los mensajes o archivos adjuntos que nos envía.</p>
                
                <p>B. Información que recopilamos cuando utiliza la plataforma Mi Softwares</p>
                
                <p><strong>Información de ubicación.</strong> Los grandes viajes comienzan con una recogida fácil y precisa. La plataforma Mi Softwares recopila información de ubicación (incluidos datos de GPS y WiFi) de manera diferente según la configuración de la aplicación Mi Softwares y los permisos del dispositivo, así como si está utilizando la plataforma como pasajero o conductor:</p>
                
                <ul>
                <li>Pasajeadores: recopilamos la ubicación precisa de su dispositivo cuando abre y usa la aplicación Mi Softwares, incluso mientras la aplicación se ejecuta en segundo plano desde el momento en que solicita un viaje hasta que finaliza. Mi Softwares también rastrea la ubicación precisa de scooters y bicicletas eléctricas en todo momento.</li>
                <li>Controladores: recopilamos la ubicación precisa de su dispositivo cuando abre y usa la aplicación, incluso mientras la aplicación se ejecuta en segundo plano cuando está en modo de controlador. También recopilamos la ubicación precisa durante un tiempo limitado después de que usted sale del modo de conductor para detectar incidentes en el viaje y continuamos recogiéndola hasta que un incidente reportado o detectado ya no esté activo.</li>
                </ul>
                
                <p><strong>Información de uso.</strong> Recopilamos información sobre su uso de la plataforma Mi Softwares, incluida información del viaje como fecha, hora, destino, distancia, ruta, pago y si utilizó una promoción o referencia. código. También recopilamos información sobre sus interacciones con la Plataforma Mi Softwares, como nuestras aplicaciones y sitios web, incluidas las páginas y el contenido que ve y las fechas y horas de su uso.<br />
                <br />
                <strong>Información del dispositivo.</strong> Recopilamos información sobre los dispositivos que utiliza para acceder a la plataforma Mi Softwares, incluido el modelo del dispositivo, la dirección IP, el tipo de navegador, la versión del sistema operativo, la identidad del proveedor y fabricante, el tipo de radio ( como 4G), preferencias y configuraciones (como el idioma preferido), instalaciones de aplicaciones, identificadores de dispositivos, identificadores de publicidad y tokens de notificaciones push. Si es conductor, también recopilamos datos de sensores móviles de su dispositivo (como velocidad, dirección, altura, aceleración, desaceleración y otros datos técnicos).<br />
                <br />
                <strong>Comunicaciones entre pasajeros y conductores.</strong> Trabajamos con un tercero para facilitar las llamadas telefónicas y los mensajes de texto entre pasajeros y conductores sin compartir el número de teléfono real de ninguna de las partes con la otra. Pero mientras utilizamos un tercero para proporcionar el servicio de comunicación, recopilamos información sobre estas comunicaciones, incluidos los números de teléfono de los participantes, la fecha y hora, y el contenido de los mensajes SMS. Por motivos de seguridad, también podemos monitorear o registrar el contenido de las llamadas telefónicas realizadas a través de la Plataforma Mi Softwares, pero siempre le informaremos que estamos a punto de hacerlo antes de que comience la llamada.<br />
                <br />
                <strong>Contactos de la libreta de direcciones.</strong> Puede configurar los permisos de su dispositivo para otorgar acceso a Mi Softwares a sus listas de contactos y dirigir a Mi Softwares para que acceda a su lista de contactos, por ejemplo, para ayudarlo a recomendar Mi Softwares a sus amigos. Si hace esto, accederemos y almacenaremos los nombres y la información de contacto de las personas en su libreta de direcciones.<br />
                <br />
                <strong>Cookies, análisis y tecnologías de terceros.</strong> Recopilamos información mediante el uso de "cookies", píxeles de seguimiento, herramientas de análisis de datos como Google Analytics, SDK y otras tecnologías de terceros para comprender cómo usted navegue a través de la plataforma Mi Softwares e interactúe con los anuncios de Mi Softwares para hacer su experiencia con Mi Softwares más segura, para saber qué contenido es popular, para mejorar su experiencia en el sitio, para ofrecerle mejores anuncios en otros sitios y para guardar sus preferencias. Las cookies son pequeños archivos de texto que los servidores web colocan en su dispositivo; están diseñados para almacenar información básica y ayudar a los sitios web y aplicaciones a reconocer su navegador. Podemos utilizar tanto cookies de sesión como cookies persistentes. Una cookie de sesión desaparece después de cerrar su navegador. Una cookie persistente permanece después de cerrar su navegador y se puede acceder a ella cada vez que utiliza la Plataforma Mi Softwares. Debe consultar su(s) navegador(es) web para modificar la configuración de cookies. Tenga en cuenta que si elimina o elige no aceptar nuestras cookies, puede perderse ciertas funciones de Mi Softwares Platform.</p>
                
                <p>C. Información que recopilamos de terceros</p>
                
                <p><strong>Servicios de terceros.</strong> Los servicios de terceros nos brindan la información necesaria para aspectos centrales de la plataforma Mi Softwares, así como para servicios, programas, beneficios de lealtad y promociones adicionales que pueden mejorar Tu experiencia con Mi Software. Estos servicios de terceros incluyen proveedores de verificación de antecedentes, socios de seguros, proveedores de servicios financieros, proveedores de marketing y otras empresas. Obtenemos la siguiente información sobre usted a partir de estos servicios de terceros:</p>
                
                <ul>
                <li>Información para hacer que Mi Softwares Platform sea más segura, como información de verificación de antecedentes para los conductores;</li>
                <li>Información sobre su participación en programas de terceros que brindan cosas como cobertura de seguro e instrumentos financieros, como información sobre seguros, pagos, transacciones y detección de fraude;</li>
                <li>Información para poner en funcionamiento programas o aplicaciones, servicios o funciones promocionales y de fidelización que usted elija conectar o vincular a su cuenta de Mi Softwares, como información sobre su uso de dichos programas, aplicaciones, servicios o funciones; y</li>
                <li>Información sobre usted proporcionada por servicios específicos, como información demográfica y de segmento de mercado.</li>
                </ul>
                
                <p><strong>Programas empresariales.</strong> Si utiliza Mi Softwares a través de su empleador u otra organización que participa en uno de nuestros programas empresariales Mi Softwares Business, recopilaremos información sobre usted de esas partes, como su nombre. e información de contacto.<br />
                <br />
                <strong>Servicio de conserjería.</strong> A veces, otra empresa o entidad puede solicitarle un viaje de Mi Softwares. Si una organización ha solicitado un viaje para usted utilizando nuestro servicio de Concierge, nos proporcionarán su información de contacto y el lugar de recogida y devolución de su viaje.<br />
                <br />
                <strong>Programas de recomendación.</strong> Los amigos ayudan a sus amigos a utilizar la plataforma Mi Softwares. Si alguien lo refiere a Mi Softwares, recopilaremos información sobre usted a partir de esa referencia, incluido su nombre e información de contacto.<br />
                <br />
                <strong>Otros usuarios y fuentes.</strong> Otros usuarios o fuentes públicas o de terceros, como autoridades policiales, aseguradoras, medios de comunicación o peatones, pueden proporcionarnos información sobre usted, por ejemplo, como parte de una investigación sobre un incidente o para brindarle soporte.</p>
                
                <p>Cómo utilizamos su información</p>
                
                <p>Utilizamos su información personal para:</p>
                
                <ul>
                <li>Proporcionar la plataforma Mi Softwares;</li>
                <li>Mantener la seguridad de la Plataforma Mi Softwares y sus usuarios;</li>
                <li>Crear y mantener la comunidad Mi Softwares;</li>
                <li>Proporcionar soporte al cliente;</li>
                <li>Mejorar la plataforma Mi Softwares; y</li>
                <li>Responder a procedimientos y obligaciones legales.</li>
                </ul>
                
                <p><strong>Proporcionar la plataforma Mi Softwares.</strong> Usamos su información personal para brindarle una experiencia intuitiva, útil, eficiente y valiosa en nuestra plataforma. Para ello, utilizamos su información personal para:</p>
                
                <ul>
                <li>Verificar su identidad y mantener su cuenta, configuración y preferencias;</li>
                <li>Conectarte con tus recorridos y realizar un seguimiento de su progreso;</li>
                <li>Calcular precios y procesar pagos;</li>
                <li>Permitir que los pasajeros y los conductores se conecten con respecto a su viaje y elijan compartir su ubicación con otros;</li>
                <li>Comunicarnos con usted sobre sus viajes y su experiencia;</li>
                <li>Recopilar comentarios sobre su experiencia;</li>
                <li>Facilitar servicios y programas adicionales con terceros; y</li>
                <li>Operar concursos, sorteos y otras promociones.</li>
                </ul>
                
                <p><strong>Mantener la seguridad de la plataforma Mi Softwares y sus usuarios.</strong> Brindarle una experiencia segura es el motor de nuestra plataforma, tanto en la carretera como en nuestras aplicaciones. Para ello, utilizamos su información personal para:</p>
                
                <ul>
                <li>Autenticar usuarios;</li>
                <li>Verificar que los conductores y sus vehículos cumplan con los requisitos de seguridad;</li>
                <li>Investigar y resolver incidentes, accidentes y reclamaciones de seguros;</li>
                <li>Fomentar un comportamiento de conducción seguro y evitar actividades inseguras;</li>
                <li>Encontrar y prevenir fraudes; y</li>
                <li>Bloquear y eliminar usuarios inseguros o fraudulentos de Mi Softwares Platform.</li>
                </ul>
                
                <p><strong>Construcción y mantenimiento de la comunidad Mi Softwares.</strong> Mi Softwares trabaja para ser una parte positiva de la comunidad. Usamos su información personal para:</p>
                
                <ul>
                <li>Comunicarnos con usted sobre eventos, promociones, elecciones y campañas;</li>
                <li>Personalizar y proporcionar contenido, experiencias, comunicaciones y publicidad para promover y hacer crecer la Plataforma Mi Softwares; y</li>
                <li>Ayude a facilitar las donaciones que elija realizar a través de la plataforma Mi Softwares.</li>
                </ul>
                
                <p><strong>Brindar atención al cliente.</strong> Trabajamos arduamente para brindar la mejor experiencia posible, incluido el soporte cuando lo necesita. Para ello, utilizamos su información personal para:</p>
                
                <ul>
                <li>Investigar y ayudarlo a resolver preguntas o problemas que tenga con respecto a la Plataforma Mi Softwares; y</li>
                <li>Brindarle apoyo o responderle.</li>
                </ul>
                
                <p><strong>Mejorando la plataforma Mi Softwares</strong>. Siempre estamos trabajando para mejorar su experiencia y brindarle funciones nuevas y útiles. Para ello, utilizamos su información personal para:</p>
                
                <ul>
                <li>Realizar investigaciones, pruebas y análisis;</li>
                <li>Desarrollar nuevos productos, funciones, asociaciones y servicios;</li>
                <li>Prevenir, encontrar y resolver errores y problemas de software o hardware; y</li>
                <li>Supervisar y mejorar nuestras operaciones y procesos, incluidas prácticas de seguridad, algoritmos y otros modelos.</li>
                </ul>
                
                <p><strong>Respuesta a Procedimientos y Requisitos Legales.</strong> A veces la ley, las entidades gubernamentales u otros organismos reguladores nos imponen demandas y obligaciones con respecto a los servicios que buscamos brindar. En tal circunstancia, podemos utilizar su información personal para responder a esas demandas u obligaciones.</p>
                
                <p>Cómo compartimos su información</p>
                
                <p>No vendemos su información personal. Para que la Plataforma Mi Softwares funcione, es posible que necesitemos compartir su información personal con otros usuarios, terceros y proveedores de servicios. Esta sección explica cuándo y por qué compartimos su información.</p>
                
                <p>A. Compartir entre usuarios de Mi Softwares</p>
                
                <p>Pasajeros y conductores.<br />
                <br />
                <strong>Información del pasajero compartida con el conductor:</strong> Al recibir una solicitud de viaje, compartimos con el conductor la ubicación de recogida del pasajero, el nombre, la foto de perfil, la calificación y las estadísticas del pasajero (como el número aproximado de viajes y años como pasajero). e información que el ciclista incluye en su perfil de ciclista (como pronombres preferidos). Al momento de la recogida y durante el viaje, compartimos con el conductor el destino del pasajero y cualquier parada adicional que el pasajero ingrese en la aplicación Mi Softwares. Una vez finalizado el viaje, también compartimos la calificación y los comentarios del pasajero con el conductor. (Eliminamos la identidad del Usuario asociada con calificaciones y comentarios cuando la compartimos con los Conductores, pero un Conductor puede identificar al Usuario que proporcionó la calificación o los comentarios).<br />
                <br />
                <strong>Información del conductor compartida con el pasajero:</strong> cuando un conductor acepta un viaje solicitado, compartiremos con el conductor el nombre del conductor, la foto de perfil, los pronombres preferidos, la calificación, la ubicación en tiempo real y la marca y modelo del vehículo. , color y matrícula, así como otra información en el perfil de Mi Softwares del conductor, como información que los conductores eligen agregar (como la bandera del país y por qué conduce) y estadísticas del conductor (como el número aproximado de viajes y años como conductor). .<br />
                <br />
                Aunque ayudamos a los Pasajeros y Conductores a comunicarse entre sí para concertar una recogida, no compartimos su número de teléfono real ni otra información de contacto con otros usuarios. Si nos informa sobre un artículo perdido o encontrado, intentaremos conectarlo con el Pasajero o Conductor correspondiente, incluso compartiremos información de contacto real con su consentimiento.<br />
                <br />
                <strong>Usuarios de viajes compartidos.</strong> Cuando los pasajeros utilizan un viaje compartido de Mi Softwares, compartimos el nombre y la foto de perfil de cada pasajero para garantizar la seguridad. Los pasajeros también pueden ver los lugares de partida y regreso de los demás como parte de conocer la ruta mientras comparten el viaje.<br />
                <br />
                <strong>Viajes solicitados o pagados por otros.</strong> Es posible que otros soliciten o paguen algunos viajes que usted realice. Si realiza uno de esos viajes utilizando su cuenta de perfil comercial de Mi Software, un código o cupón, un programa subsidiado (por ejemplo, tránsito o gobierno) o una tarjeta de crédito corporativa vinculada a otra cuenta, u otro usuario solicita o paga de otro modo un viaje para usted, podemos compartir algunos o todos los detalles de su viaje con esa otra parte, incluida la fecha, hora, cargo, calificación otorgada, región del viaje y lugar de recogida y devolución de su viaje.<br />
                <br />
                <strong>Programas de recomendación.</strong> Si recomienda a alguien a la plataforma Mi Softwares, le haremos saber que usted generó la recomendación. Si otro usuario lo recomendó, podemos compartir información sobre su uso de la Plataforma Mi Softwares con ese usuario. Por ejemplo, una fuente de referencia puede recibir una bonificación cuando se une a la plataforma Mi Softwares o completa una cierta cantidad de viajes y recibiría dicha información.</p>
                
                <p>B. Compartir con proveedores de servicios externos con fines comerciales</p>
                
                <p>Dependiendo de si usted es un Pasajero o un Conductor, Mi Softwares puede compartir las siguientes categorías de su información personal con fines comerciales para brindarle una variedad de funciones y servicios de la Plataforma Mi Softwares:</p>
                
                <ul>
                <li>Identificadores personales, como su nombre, dirección, dirección de correo electrónico, número de teléfono, fecha de nacimiento, número de identificación gubernamental (como el número de seguro social), información de la licencia de conducir, información del vehículo e información del seguro del automóvil;</li>
                <li>Información financiera, como números de ruta bancaria, información fiscal y cualquier otra información de pago que nos proporcione;</li>
                <li>Información comercial, como información de viajes, estadísticas y comentarios del conductor/pasajero e historial de transacciones del conductor/pasajero;</li>
                <li>Información de actividad de Internet u otra red electrónica, como su dirección IP, tipo de navegador, versión del sistema operativo, operador y/o fabricante, identificadores de dispositivo e identificadores de publicidad móvil; y</li>
                <li>Datos de ubicación.</li>
                </ul>
                
                <p>Revelamos esas categorías de información personal a proveedores de servicios para cumplir con los siguientes fines comerciales:</p>
                
                <ul>
                <li>Mantener y dar servicio a su cuenta de Mi Softwares;</li>
                <li>Procesar o realizar viajes;</li>
                <li>Brindarle servicio al cliente;</li>
                <li>Procesamiento de transacciones Rider;</li>
                <li>Procesamiento de solicitudes y pagos de conductores;</li>
                <li>Verificar la identidad de los usuarios;</li>
                <li>Detección y prevención del fraude;</li>
                <li>Procesamiento de reclamaciones de seguros;</li>
                <li>Ofrecer programas promocionales y de fidelización de conductores;</li>
                <li>Proporcionar servicios de marketing y publicidad a Mi Softwares;</li>
                <li>Proporcionar financiación;</li>
                <li>Proporcionar servicios de emergencia solicitados;</li>
                <li>Proporcionar servicios de análisis a Mi Softwares; y</li>
                <li>Realizar una investigación interna para desarrollar la plataforma Mi Softwares.</li>
                </ul>
                
                <p>C. Por razones legales y para proteger la plataforma Mi Softwares</p>
                
                <ul>
                <li>Cumplir con cualquier ley o regulación federal, estatal o local aplicable, investigación civil, penal o regulatoria, investigación o proceso legal, o solicitud gubernamental ejecutable;</li>
                <li>Responder a un proceso legal (como una orden de registro, citación, citación u orden judicial);</li>
                <li>Hacer cumplir nuestros Términos de servicio;</li>
                <li>Cooperar con las agencias de aplicación de la ley en relación con conductas o actividades que razonablemente y de buena fe creemos que pueden violar las leyes federales, estatales o locales; o</li>
                <li>Ejercer o defender reclamaciones legales, proteger contra daños a nuestros derechos, propiedad, intereses o seguridad o los derechos, propiedad, intereses o seguridad de usted, terceros o el público según lo requiera o permita la ley.</ li>
                </ul>
                
                <p>D. En relación con la venta o fusión</p>
                
                <p>Podemos compartir su información personal mientras negociamos o en relación con un cambio de control corporativo, como una reestructuración, fusión o venta de nuestros activos.</p>
                
                <p>E. Según sus indicaciones</p>
                
                <p>Con su permiso o según sus indicaciones, podemos divulgar su información personal para interactuar con un tercero o para otros fines.</p>
                
                <p>Cómo almacenamos y protegemos su información</p>
                
                <p>Conservamos su información durante el tiempo que sea necesario para proporcionarle a usted y a nuestros demás usuarios la plataforma Mi Softwares. Esto significa que mantenemos la información de su perfil mientras mantenga una cuenta. Conservamos información transaccional, como viajes y pagos, durante al menos siete años para garantizar que podamos realizar funciones comerciales legítimas, como la contabilidad de obligaciones fiscales. Si solicita la eliminación de su cuenta, eliminaremos su información como se establece en la sección "Eliminación de su cuenta" a continuación. Tomamos medidas razonables y apropiadas diseñadas para proteger su información personal. Pero ninguna medida de seguridad puede ser 100% efectiva y no podemos garantizar la seguridad de su información, incluso contra intrusiones no autorizadas o actos de terceros.</p>
                
                <p>Sus derechos y opciones con respecto a sus datos</p>
                
                <p>Mi Softwares le proporciona formas de acceder y eliminar su información personal, así como de ejercer otros derechos sobre datos que le brindan cierto control sobre su información personal.</p>
                
                <p>A. Todos los usuarios</p>
                
                <p>Suscripciones por correo electrónico. Siempre puedes darte de baja de nuestros correos electrónicos comerciales o promocionales haciendo clic en cancelar suscripción en esos mensajes. Seguiremos enviándole correos electrónicos transaccionales y relacionales sobre su uso de la Plataforma Mi Softwares.<br />
                <br />
                <strong>Mensajes de texto.</strong> Puede optar por no recibir mensajes de texto comerciales o promocionales. También puede optar por no recibir todos los mensajes de texto de Mi Softwares (incluidos los mensajes transaccionales o relacionales). Tenga en cuenta que optar por no recibir todos los mensajes de texto puede afectar su uso de la plataforma Mi Softwares. Los conductores también pueden optar por no recibir mensajes específicos del conductor enviando un mensaje de texto con la palabra STOP. en respuesta a un SMS del conductor Para volver a habilitar los mensajes de texto, puede enviar un mensaje de texto a INICIO en respuesta a un SMS de confirmación de cancelación de suscripción.<br />
                <br />
                <strong>Notificaciones push.</strong> Puede optar por no recibir notificaciones push a través de la configuración de su dispositivo. Tenga en cuenta que optar por no recibir notificaciones automáticas puede afectar su uso de la plataforma Mi Softwares (como recibir una notificación de que su viaje ha llegado).<br />
                <br />
                <strong>Información del perfil</strong>. Puede revisar y editar cierta información de la cuenta que ha elegido agregar a su perfil iniciando sesión en la configuración y el perfil de su cuenta.<br />
                <br />
                <strong>Información de ubicación.</strong> Puede evitar que su dispositivo comparta información de ubicación a través de la configuración del sistema de su dispositivo. Pero si lo hace, esto puede afectar la capacidad de Mi Softwares para brindarle nuestra gama completa de funciones y servicios.<br />
                <br />
                <strong>Seguimiento de cookies.</strong> Puede modificar la configuración de cookies en su navegador, pero si elimina o elige no aceptar nuestras cookies, es posible que se esté perdiendo ciertas funciones de la plataforma Mi Softwares.<br />
                <br />
                <strong>No rastrear.</strong> Su navegador puede ofrecerle la opción “No rastrear”, que le permite indicar a los operadores de sitios web y aplicaciones y servicios web que no desea que rastreen sus actividades en línea. La plataforma Mi Softwares no admite actualmente solicitudes de No seguimiento en este momento.<br />
                <br />
                <strong>Eliminar su cuenta.</strong> Si desea eliminar su cuenta de Mi Softwares, visite nuestra página de inicio de privacidad. En algunos casos, no podremos eliminar su cuenta, por ejemplo, si hay un problema con su cuenta relacionado con la confianza, la seguridad o el fraude. Cuando eliminamos su cuenta, podemos conservar cierta información para fines comerciales legítimos o para cumplir con obligaciones legales o reglamentarias. Por ejemplo, podemos conservar su información para resolver reclamaciones de seguros abiertas, o podemos estar obligados a conservar su información como parte de una reclamación legal abierta. Cuando conservamos dichos datos, lo hacemos de manera diseñada para evitar su uso para otros fines.<br />
                <br />
                <strong>Derecho a saber.</strong> Tiene derecho a saber y ver qué datos hemos recopilado, incluidos:</p>
                
                <ul>
                <li>Las categorías de información personal que hemos recopilado sobre usted;</li>
                <li>Las categorías de fuentes de las que se recopila la información personal;</li>
                <li>El propósito comercial o empresarial para recopilar su información personal;</li>
                <li>Las categorías de terceros con quienes hemos compartido su información personal; y</li>
                <li>Los datos personales específicos que hemos recopilado sobre usted.</li>
                </ul>
                
                <p><strong>Derecho a eliminar.</strong> Tiene derecho a solicitar que eliminemos la información personal que hemos recopilado de usted (y solicitar a nuestros proveedores de servicios que hagan lo mismo). Sin embargo, existen una serie de excepciones que incluyen, entre otras, cuando la información es necesaria para que nosotros o un tercero realice cualquiera de las siguientes acciones:</p>
                
                <ul>
                <li>Completa tu transacción;</li>
                <li>Proporcionarle un bien o servicio;</li>
                <li>Ejecutar un contrato entre nosotros y usted;</li>
                <li>Proteger su seguridad y procesar a los responsables de violarla;</li>
                <li>Reparar nuestro sistema en caso de error;</li>
                <li>Proteger los derechos de libertad de expresión suyos o de otros usuarios;</li>
                <li>Participar en investigaciones científicas, históricas o estadísticas públicas o revisadas por pares en interés público que cumplan con todas las demás leyes de ética y privacidad aplicables;</li>
                <li>Cumplir con una obligación legal; o</li>
                <li>Hacer otros usos internos y legales de la información que sean compatibles con el contexto en el que la proporcionó.</li>
                </ul>
                
                <p><strong>Otros derechos.</strong> Puede solicitar cierta información sobre nuestra divulgación de información personal a terceros para sus propios fines de marketing directo durante el año calendario anterior. Esta solicitud es gratuita y podrá realizarse una vez al año. También tiene derecho a no ser discriminado por ejercer cualquiera de los derechos enumerados anteriormente.<br />
                <br />
                <strong>Sitio web:</strong> Puede visitar nuestra página de inicio de privacidad para autenticarse y ejercer sus derechos a través de nuestro sitio web.<br />
                <br />
                <strong>Formulario web por correo electrónico:</strong> Puede escribirnos para ejercer sus derechos. Para responder a algunos derechos necesitaremos verificar su solicitud, ya sea pidiéndole que inicie sesión y autentique su cuenta o de otro modo verifique su identidad proporcionando información sobre usted o su cuenta. Los agentes autorizados pueden realizar una solicitud en su nombre si les ha otorgado un poder legal o si se nos proporciona prueba de permiso firmado, verificación de su identidad y confirmación de que usted le dio permiso al agente para enviar la solicitud. Tiempo y formato de respuesta. Nuestro objetivo es responder a la solicitud de acceso o eliminación de un consumidor dentro de los 45 días posteriores a la recepción de esa solicitud. Si requerimos más tiempo, le informaremos el motivo y el período de extensión por escrito.</p>
                
                <p>Datos de los niños</p>
                
                <p>Mi Softwares no está dirigido a niños y no recopilamos intencionadamente información personal de niños menores de 13 años. Si descubrimos que un niño menor de 13 años nos ha proporcionado información personal, tomaremos medidas para eliminarla. información. Si cree que un niño menor de 13 años nos ha proporcionado información personal, póngase en contacto con nosotros</p>
                
                <p>Enlaces a sitios web de terceros</p>
                
                <p>La Plataforma Mi Softwares puede contener enlaces a sitios web de terceros. Esos sitios web pueden tener políticas de privacidad diferentes a las nuestras. No somos responsables de esos sitios web y le recomendamos que revise sus políticas. Comuníquese directamente con esos sitios web si tiene alguna pregunta sobre sus políticas de privacidad.</p>
                
                <p>Cambios en esta política de privacidad</p>
                
                <p>Podemos actualizar esta política de vez en cuando a medida que la Plataforma Mi Softwares cambia y la ley de privacidad evoluciona. Si lo actualizamos, lo haremos en línea, y si realizamos cambios materiales, se lo haremos saber a través de la Plataforma Mi Softwares o mediante algún otro método de comunicación como el correo electrónico. Cuando utilizas Mi Softwares, aceptas los términos más recientes de esta política.</p>
                
                <p>Contáctenos</p>
                
                <p>Si tiene alguna pregunta o inquietud sobre su privacidad o cualquier aspecto de esta política, incluso si necesita acceder a esta política en un formato alternativo, le recomendamos que se comunique con nosotros.</p>',
            'terms_title' => 'Términos y condiciones',
            'terms' => '<h2><strong>Términos y condiciones</strong></h2>

                <p>ACUERDO DE LICENCIA DE USUARIO FINAL</p>

                <p>Última actualización 16 de mayo de 2021</p>

                <p>Mi Softwares,LLC tiene licencia para usted (usuario final) otorgada por Mi Softwares, LLC, ubicada en 6255 Towncenter Drive Ste 819, Clemmons, Carolina del Norte 27012, Estados Unidos (en adelante: Licenciante), para su uso únicamente según los términos de este Acuerdo de Licencia.<br />
                <br />
                Al descargar la Aplicación de Apple AppStore y Google Play, y cualquier actualización de la misma (según lo permita este Acuerdo de licencia), usted indica que acepta estar sujeto a todos los términos y condiciones de este Acuerdo de licencia, y que acepta este Acuerdo de licencia.<br />
                <br />
                Las partes de este Acuerdo de licencia reconocen que Apple y/o Google Play no son Partes de este Acuerdo de licencia y no están sujetos a ninguna disposición u obligación con respecto a la Aplicación, como garantía, responsabilidad, mantenimiento y soporte de la misma. Mi Softwares, LLC, no Apple ni Google Play, es el único responsable de la Aplicación con licencia y su contenido.<br />
                <br />
                Es posible que este Acuerdo de licencia no establezca reglas de uso para la Aplicación que entren en conflicto con los Términos de servicio más recientes de la App Store. Mi Softwares, LLC reconoce que tuvo la oportunidad de revisar dichos términos y que este Acuerdo de licencia no entra en conflicto con ellos.<br />
                <br />
                Todos los derechos que no se le otorgan expresamente están reservados.</p>

                <p>1. LA APLICACIÓN</p>

                <p>Mi Softwares (en adelante: Aplicación) es un software que es una plataforma de viajes compartidos y está personalizado para dispositivos móviles Apple y Android. Se utiliza para conectar pasajeros con conductores para llegar del punto A al B con solo presionar un botón.<br />
                <br />
                La Aplicación no está diseñada para cumplir con regulaciones específicas de la industria (Ley de Responsabilidad y Portabilidad de Seguros Médicos (HIPAA), Ley Federal de Gestión de Seguridad de la Información (FISMA), etc.), por lo que si sus interacciones estuvieran sujetas a dichas leyes, no podrá utilice esta aplicación. No puede utilizar la Aplicación de una manera que viole la Ley Gramm-Leach-Bliley (GLBA).</p>

                <p>2. ALCANCE DE LA LICENCIA</p>

                <p>2.1 Se le otorga una licencia intransferible, no exclusiva y que no se puede sublicenciar para instalar y utilizar la Aplicación con licencia en cualquier Producto de la marca Apple o de Google que Usted (Usuario final) posea o controle y según lo permita el Reglas de uso establecidas en esta sección y en los Términos de servicio de la App Store, con la excepción de que otras cuentas asociadas con usted (Usuario final, El Comprador) pueden acceder a dicha Aplicación con licencia y utilizarla a través de Family Sharing o por volumen. compra.<br />
                <br />
                2.2 Esta licencia también regirá cualquier actualización de la Aplicación proporcionada por el Licenciante que reemplace, repare y/o complemente la primera Aplicación, a menos que se proporcione una licencia separada para dicha actualización, en cuyo caso regirán los términos de esa nueva licencia.<br />
                <br />
                2.3 No puede compartir ni poner la Aplicación a disposición de terceros (a menos que en la medida permitida por los Términos y condiciones de Apple y con el consentimiento previo por escrito de Mi Softwares, LLC), vender, alquilar, prestar, arrendar o redistribuir de otro modo la Aplicación. <br />
                <br />
                2.4 No puede realizar ingeniería inversa, traducir, desensamblar, integrar, descompilar, integrar, eliminar, modificar, combinar, crear trabajos derivados o actualizaciones, adaptar o intentar derivar el código fuente de la Aplicación, o cualquier parte de la misma (excepto con consentimiento previo por escrito de Mi Softwares, LLC).<br />
                <br />
                2.5 No puede copiar (excepto cuando lo autorice expresamente esta licencia y las Reglas de uso) ni alterar la Aplicación o partes de la misma. Puede crear y almacenar copias solo en dispositivos que posee o controla para mantener copias de seguridad según los términos de esta licencia, los Términos de servicio de la App Store y cualquier otro término y condición que se aplique al dispositivo o software utilizado. No puede eliminar ningún aviso de propiedad intelectual. Usted reconoce que ningún tercero no autorizado puede obtener acceso a estas copias en cualquier momento. <Br />
                <br />
                2.6 Las violaciones de las obligaciones mencionadas anteriormente, así como el intento de dicha infracción, pueden estar sujetas a enjuiciamiento y daños. <Br />
                <br />
                2.7 El licenciatura se reserva el derecho de modificar los términos y condiciones de licencia. <Br />
                <br />
                2.8 Nada en esta licencia debe interpretarse para restringir los términos de terceros. Cuando use la aplicación, debe asegurarse de cumplir con los términos y condiciones de terceros aplicables. </p>

                <p> 3. Requisitos técnicos </p>

                <p> 3.1 El licenciatura intenta mantener la aplicación actualizada para que cumpla con versiones modificadas/nuevas del firmware y el nuevo hardware. No se le otorgan derechos para reclamar dicha actualización. <Br />
                <br />
                3.2 Usted reconoce que es su responsabilidad confirmar y determinar que el dispositivo de usuario final de la aplicación en el que tiene la intención de usar la aplicación satisface las especificaciones técnicas mencionadas anteriormente. <Br />
                <br />
                3.3 El licenciatura se reserva el derecho de modificar las especificaciones técnicas como lo ve apropiado en cualquier momento. </p>

                <p> 4. Mantenimiento y soporte </p>

                <p> 4.1 El licenciatura es el único responsable de proporcionar servicios de mantenimiento y soporte para esta aplicación licenciada. Puede comunicarse con el licenciante en la dirección de correo electrónico que figura en la App Store o la descripción general de Google Play para esta aplicación con licencia. <Br />
                <br />
                4.2 MI Softwares, LLC y el usuario final reconocen que Apple y Google Play no tienen ninguna obligación de proporcionar ningún servicio de mantenimiento y soporte con respecto a la aplicación con licencia. </p>

                <p> 5. Uso de datos </p>

                <p> Usted reconoce que el licenciatura podrá acceder y ajustar su contenido de aplicación con licencia descargada y su información personal, y que el uso del licenciatura de dicho material e información está sujeto a sus acuerdos legales con la Política de privacidad del licenciatura y el licenciante: http: // www.mi softwares.us/privacy. </p>

                <p> 6. Contribuciones generadas por el usuario </p>

                <p> La aplicación puede invitarlo a chatear, contribuir o participar en blogs, tableros de mensajes, foros en línea y otra funcionalidad, y puede brindarle la oportunidad de crear, enviar, publicar, mostrar, transmitir, realizar, publicar , distribuir o transmitir contenido y materiales para nosotros o en la aplicación, incluidos, entre otros, texto, escritos, video, audio, fotografías, gráficos, comentarios, sugerencias o información personal u otro material (colectivamente, "contribuciones"). Otros usuarios de la aplicación y a través de sitios web o aplicaciones de terceros. Como tal, cualquier contribución que transmita puede tratarse como no confidencial y no propietario. Cuando crea o ponga a disposición cualquier contribución, usted representa y garantiza que: <Br />
                <br />
                1. o derechos morales de un tercero. <Br />
                <br />
                2. Usted es el creador y propietario de o tiene las licencias, derechos, consentimientos, lanzamientos y permisos necesarios para usar y autorizarnos, la aplicación y otros usuarios de la aplicación para usar sus contribuciones de cualquier manera contemplada por la aplicación y estos términos de uso. <Br />
                <br />
                3. Usted tiene el consentimiento, liberación y/o permiso por escrito de cada persona individual identificable en sus contribuciones para usar el nombre o la semejanza o cada persona individual identificable para permitir la inclusión y el uso de sus contribuciones de cualquier manera contemplada por la aplicación y estos Términos de uso. <Br />
                <br />
                4. Tus contribuciones no son falsas, inexactas o engañosas. <Br />
                <br />
                5. Sus contribuciones no son publicidad no solicitada o no autorizada, materiales promocionales, esquemas piramidales, cartas de cadena, spam, correos masivos u otras formas de solicitud. <Br />
                <br />
                6. Sus contribuciones no son obscenas, lascadas, lascivas, sucias, violentas, acosadoras, difamatorias, calumnias o de otra manera objetables (según lo determine nosotros). <Br />
                <br />
                7. Tus contribuciones no ridiculizan, se burlan, menosprecian, intimidan o abusan a nadie. <Br />
                <br />
                8. Sus contribuciones no se usan para acosar o amenazar (en el sentido legal de esos términos) a ninguna otra persona y para promover la violencia contra una persona o clase específica de personas. <Br />
                <br />
                9. Sus contribuciones no violen ninguna ley, regulación o regla aplicable. <Br />
                <br />
                10. Sus contribuciones no violan los derechos de privacidad o publicidad de ningún tercero. <Br />
                <br />
                11. Sus contribuciones no contienen ningún material que solicite información personal de cualquier persona menor de 18 años o explote a las personas menores de 18 años de manera sexual o violenta. <Br />
                <br />
                12. Sus contribuciones no violan ninguna ley aplicable con respecto a la pornografía infantil, o de otra manera destinada a proteger la salud o el bienestar de los menores. <Br />
                <br />
                13. Sus contribuciones no incluyen ningún comentario ofensivo que esté conectado a la raza, el origen nacional, el género, la preferencia sexual o la discapacidad física. <Br />
                <br />
                14. Sus contribuciones no violan de otra manera, ni se vinculan con el material que viola, cualquier disposición de estos Términos de uso, o cualquier ley o regulación aplicable. <Br />
                <br />
                Cualquier uso de la aplicación en violación de lo anterior viola estos Términos de uso y puede dar lugar, entre otras cosas, la terminación o la suspensión de sus derechos para usar la aplicación. </p>

                <p> 7. Licencia de contribución </p>

                <p> Al publicar sus contribuciones a cualquier parte de la aplicación o hacer que las contribuciones sean accesibles para la aplicación vinculando su cuenta de la aplicación a cualquiera de sus cuentas de redes sociales, usted otorga automáticamente y representa y garantiza que tiene derecho a concesión, para nosotros, un derecho sin restricciones, ilimitado, irrevocable, perpetuo, no exclusivo, transferible, libre de regalías, totalmente pagado, derecho mundial y licencia para alojar, usar copiar, reproducir, revelar, vender, revender, publicar, emitir un yeso amplio , Retitar, archivar, almacenar, caché, exhibir públicamente, reformatear, traducir, transmitir, extracto (en su totalidad o en parte) y distribuir dichas contribuciones (incluidas, entre otros, su imagen y voz) para cualquier propósito, publicidad comercial o de otra manera. , y para preparar obras derivadas o incorporar en otras obras, como contribuciones, y otorgar y autorizar sublicenses de lo anterior. El uso y la distribución pueden ocurrir en cualquier formato de medios y a través de cualquier canal de medios. <Br />
                <br />
                Esta licencia se aplicará a cualquier formulario, medios o tecnología ahora conocido o en adelante desarrollado, e incluye nuestro uso de su nombre, nombre de la empresa y nombre de la franquicia, según corresponda, y cualquiera de las marcas comerciales, marcas de servicio, nombres comerciales, logotipos, logotipos, e imágenes personales y comerciales que proporciona. No renuncia a todos los derechos morales en sus contribuciones, y garantiza que los derechos morales no se han afirmado en sus contribuciones. <Br />
                <br />
                No afirmamos ninguna propiedad sobre sus contribuciones. Retiene la propiedad total de todas sus contribuciones y cualquier derecho de propiedad intelectual u otros derechos de propiedad asociados con sus contribuciones. No somos responsables de ninguna declaración o representación en sus contribuciones proporcionadas por usted en cualquier área de la solicitud. Usted es el único responsable de sus contribuciones a la solicitud y acepta expresamente exonerarnos de cualquier responsabilidad y abstenerse de cualquier acción legal contra nosotros con respecto a sus contribuciones. <Br />
                <br />
                Tenemos el derecho, a nuestra sola y absoluta discreción, (1) para editar, redactar o cambiar cualquier contribución; (2) volver a clasificar cualquier contribución para colocarlos en ubicaciones más apropiadas en la aplicación; y (3) previamente o eliminar cualquier contribución en cualquier momento y por cualquier motivo, sin previo aviso. No tenemos la obligación de monitorear sus contribuciones. </p>

                <p> 8. Responsabilidad </p>

                <p> 8.1 La responsabilidad del licenciante en el caso de violación de las obligaciones y el agravio se limitará a la intención y la grave negligencia. Solo en el caso de un incumplimiento de los deberes contractuales esenciales (obligaciones cardinales), el licenciante también será responsable en caso de ligera negligencia. En cualquier caso, la responsabilidad se limitará a los daños previsibles y contractualmente típicos. La limitación mencionada anteriormente no se aplica a las lesiones a la vida, a las extremidades o una salud. <Br />
                <br />
                8.2 El licenciatura no toma responsabilidad ni responsabilidad por los daños causados ​​por un incumplimiento de los deberes de acuerdo con la Sección 2 de este Acuerdo. Para evitar la pérdida de datos, debe utilizar funciones de respaldo de la aplicación en la medida permitida por los términos y condiciones de uso de terceros aplicables. Usted es consciente de que en caso de alteraciones o manipulaciones de la aplicación, no tendrá acceso a la aplicación con licencia. </p>

                <p> 9. Garantía </p>

                <p> 9.1 El licenciatura garantiza que la aplicación está libre de spyware, caballos troyanos, virus o cualquier otro malware al momento de su descarga. El licenciatura garantiza que la aplicación funciona como se describe en la documentación del usuario. <Br />
                <br />
                9.2 No se proporciona garantía para la aplicación que no se puede ejecutar en el dispositivo, que se ha modificado, manejado de manera inapropiada no autorizada de manera inapropiada o definitiva, combinada o instalada con hardware o software inapropiado, utilizado con accesorios inapropiados, independientemente de si es por terceros, por terceros, o si hay alguna otra razón fuera de los software de MI, la esfera de influencia de LLC que afecta la ejecución de la aplicación. <Br />
                <br />
                9.3 Debe inspeccionar la aplicación inmediatamente después de instalarla y notificar a MI Softwares, LLC sobre los problemas descubiertos sin demora por correo electrónico proporcionado en las reclamaciones de productos. El informe del defecto se tendrá en cuenta e investigará más a fondo si se ha enviado por correo dentro de un período de noventa (90) días después del descubrimiento. <Br />
                <br />
                9.4 Si confirmamos que la aplicación es defectuosa, MI Softwares, LLC se reserva una opción para remediar la situación, ya sea por medio de la entrega del defecto o sustituto. <Br />
                <br />
                9.5 En caso de que cualquier falla de la solicitud se ajuste a cualquier garantía aplicable, puede notificar al operador de la tienda de aplicaciones y el precio de la compra de su solicitud se le reembolsará. En la medida máxima permitida por la ley aplicable, el operador de la tienda de aplicaciones no tendrá otra obligación de garantía con respecto a la aplicación, y cualquier otra pérdida, reclamo, daños, pasivos, gastos y costos atribuibles a cualquier negligencia para cumplir con cualquier Garantía. <Br />
                <br />
                9.6 Si el usuario es un emprendedor, cualquier reclamo basado en fallas expira después de un período legal de limitación que asciende a doce (12) meses después de que la aplicación se puso a disposición del usuario. Los períodos legales de limitación dados por la ley se aplican a los usuarios que son consumidores. </p>

                <p> 10. Reclamaciones de productos </p>

                <p> mi Softwares, LLC y el usuario final reconocen que MI Softwares, LLC, y no Apple, es responsable de abordar cualquier reclamo del usuario final o cualquier tercero relacionado con la solicitud con licencia o la posesión del usuario final y la posesión del usuario final /o el uso de esa aplicación con licencia, que incluye, entre otros: <Br />
                <br />
                (i) reclamos de responsabilidad del producto; <Br />
                <br />
                (ii) cualquier reclamo de que la solicitud con licencia no se ajuste a ningún requisito legal o reglamentario aplicable; y <Br />
                <br />
                (iii) Reclamaciones que surgen bajo protección del consumidor, privacidad o legislación similar, incluso en relación con el uso de su aplicación con licencia. </p>

                <p> 11. Cumplimiento legal </p>

                <p> usted representa y garantiza que no se encuentra en un país que está sujeto a un embargo del gobierno de los EE. UU., o que ha sido designado por el gobierno de los Estados Unidos como un país de "apoyo terrorista"; y que no figura en ninguna lista del gobierno de los EE. UU. De partes prohibidas o restringidas. </p>

                <p> 12. Información de contacto </p>

                <p> Para consultas generales, quejas, preguntas o reclamos sobre la solicitud con licencia, comuníquese con: <Br />
                <br />
                <strong> mi softwares, LLC <Br />
                6255 TownCenter Drive Ste 819 <Br />
                Clemmons, NC 27012 <Br />
                Estados Unidos <Br />
                Support@mi softwares.us </strong> </p>

                <p> 13. Terminación </p>

                <p> La licencia es válida hasta que MI Softwares, LLC o por usted. Sus derechos bajo esta licencia terminarán automáticamente y sin previo aviso de MI Softwares, LLC, si no se adhiere a ningún término de esta licencia. Tras la terminación de la licencia, detendrá todo el uso de la solicitud y destruirá todas las copias, completas o parciales de la aplicación. </p>

                <p> 14. Términos de acuerdos y beneficiarios de terceros </p>

                <p> mi softwares, LLC representa y garantiza que MI Softwares, LLC cumplirá con los términos de acuerdo de terceros aplicables cuando se use la aplicación con licencia. <Br />
                <br />
                De acuerdo con la Sección 9 de "Instrucciones para términos mínimos del acuerdo de licencia de usuario final del desarrollador", las subsidiarias de Apple y Apple serán beneficiarios de este acuerdo de licencia del usuario final y, al aceptar los términos y condiciones de esta licencia Acuerdo, Apple tendrá el derecho (y se considerará que ha aceptado el derecho) para hacer cumplir este acuerdo de licencia de usuario final contra usted como un tercero beneficiario de la misma. </p>.

                <p> 15. Derechos de propiedad intelectual </p>

                <p> MI Softwares, LLC y el usuario final reconocen que, en el caso de cualquier reclamo de terceros, la solicitud con licencia o la posesión del usuario final y el uso de esa solicitud con licencia infringen los derechos de propiedad intelectual del tercero, MI Softwares, LLC, y no Apple, será el único responsable de la investigación, defensa, liquidación y alta o cualquier reclamación de infracción de propiedad intelectual. </p>

                <p> 16. Ley aplicable </p>

                <p> Este acuerdo de licencia se rige por las leyes del estado de Carolina del Norte, excluyendo sus reglas de conflictos de derecho. </p>.

                <p> 17. Varios </p>

                <p> 17.1 Si alguno de los términos de este Acuerdo debe ser o inválido, la validez de las disposiciones restantes no se verá afectada. Los términos no válidos serán reemplazados por los válidos formulados de una manera que alcance el propósito principal. <Br />
                <br />
                17.2 Acuerdos, cambios y enmiendas colaterales solo son válidos si se establecen por escrito. La cláusula anterior solo se puede renunciar por escrito. </p> ',
            'cumplimiento_title' => 'cumplimiento',
            'Cumplimiento' => '<h3> <strong> Oportunidad de empleo igual y política de no discriminación </strong> </h3>

                <h3> i. Descripción general y alcance </h3>

                <p> MI Softwares, LLC de 6255 Towncenter Drive Ste 819, Clemmons, Carolina del Norte 27012, ha establecido una política de no discriminación e igualdad de oportunidades de empleo ("EEO"). Esta política de EEO se aplica a todos los aspectos de la relación entre MI Softwares, LLC y sus empleados, incluidos, entre otras, empleo, reclutamiento, anuncios para el empleo, contratación y despido, compensación, asignación, clasificación de empleados, terminación, mejora, mejoramiento, Promociones, transferencia, capacitación, condiciones de trabajo, salarios y administración salarial, y los beneficios de los empleados y la aplicación de políticas. Estas políticas se aplican a contratistas independientes, empleados temporales, todo el personal que trabaja en las instalaciones y cualquier otra persona o empresa que haga negocios para o con MI Softwares, LLC. Cualquier usuario que haya violado esta prohibición perderá acceso a la plataforma MI Softwares, LLC. Las leyes aplicables en ciertas jurisdicciones pueden requerir y/o permitir la provisión de servicios por el beneficio de una categoría específica de personas. En tales jurisdicciones, los servicios proporcionados de conformidad con estas leyes y los términos aplicables correspondientes están permitidos según esta política. </p>

                <H3> II. Políticas </h3>

                <p>1. Discriminación. </p>

                <p> mi softwares, LLC no tolerará, bajo ninguna circunstancia, sin excepción, ninguna forma de discriminación basada en la raza, el credo, la religión, el color, la edad, la discapacidad, el embarazo, el estado civil, el estado de los padres, la orientación sexual, la expresión de género, Identidad de género, estatus veterano, estatus militar, estatus de víctima de violencia doméstica, origen nacional, afiliación política, sexo, características genéticas predisponentes o ubicación geográfica y cualquier otro estado protegido por la ley. Esta lista no es exhaustiva. Para las personas calificadas con discapacidades, MI Softwares, LLC hará todo lo posible para proporcionar adaptaciones razonables en el lugar de trabajo que cumplan con las leyes aplicables. </p>

                <p> La discriminación en el proporcionar servicios de transporte está estrictamente prohibido </p>

                <p> Los conductores y empleados asociados deben conocer las prohibiciones no discriminatorias. MI Softwares, LLC no tolerará la alojamiento público, que incluye servicios de taxi -Taxicab Práctica discriminatoria para negar, directa o indirectamente, a ninguna persona el disfrute completo e igual de los bienes, servicios, instalaciones, privilegios, ventajas y alojamientos de cualquier lugar de alojamiento público (incluidos los servicios de taxis) de total o parcialmente por una razón discriminatoria basada en el lugar de residencia o negocios. </p>

                <p> Conducta discriminatoria prohibida: </p>

                <p> MI Softwares, LLC reconoce que los impulsores asociados nunca deben discriminar a ciertos clientes al no recogerlos, no llevarlos a donde desean ir o tratarlos con menos respeto en función de las características o rasgos protegidos mencionados anteriormente. Ejemplos específicos de conducta discriminatoria, incluyen lo siguiente: <Br />
                <br />
                No recoger a un pasajero sobre la base de ninguna característica o rasgo protegido, incluso no recoger a un pasajero con un animal de servicio · solicitando que un pasajero salga de un taxi en base a una característica o rasgo protegido · utilizando lenguaje despectivo o de acoso Sobre la base de una característica o rasgo protegido · Rechazando una camioneta en un área geográfica específica. </p>

                <p> Discriminación geográfica: </p>

                <p> mi softwares, LLC no tolera la discriminación geográfica y reconoce lo importante que es llevar al cliente al destino solicitado sin discriminar a ese cliente en función de dónde desea ir. Todos los impulsores asociados, empleados, gerentes, partes interesadas y agentes de MI Softwares, LLC cumplirán con estas políticas contra la discriminación. En algunos casos, las leyes y regulaciones locales pueden proporcionar mayores protecciones que las descritas en esta política. </p>

                <p> 2. Acoso </p>

                <p> mi softwares, LLC se compromete a proporcionar un entorno de trabajo libre de acoso. Cualquier comportamiento que no sea deseado y ofensivo para el destinatario, que crea un ambiente de trabajo intimidante, hostil o humillante para esa persona viola la política de MI, LLC. El acoso puede ocurrir entre los miembros del sexo opuesto o el mismo sexo. El acoso, verbal o no verbal, explícito o implícito, basado en el sexo, la raza, el origen étnico, el origen nacional, la edad, la religión de un individuo o cualquier otra característica legalmente protegida no se tolerará. Todos los empleados, incluidos los supervisores, otro personal de gestión y contratistas independientes, deben cumplir con esta política. Ninguna persona se verá afectada negativamente en el empleo con MI Softwares, LLC como resultado de presentar quejas de acoso. </p>

                <p> 3. Acoso sexual </p>

                <p> Los avances sexuales no deseados, las solicitudes de favores sexuales y otra conducta verbal o física de una naturaleza sexual constituyen acoso cuando (1) la sumisión a dicha conducta se realiza explícita o implícitamente un término o condición de empleo; (2) la sumisión o rechazo de dicha conducta por parte de un individuo se utiliza como base para las decisiones de empleo, promoción, transferencia, selección para capacitación, evaluaciones de desempeño, beneficios u otros términos y condiciones de empleo; o (3) dicha conducta tiene el propósito o efecto de crear un entorno de trabajo intimidante, hostil u ofensivo o interfiere sustancialmente con el desempeño laboral de un empleado. MI Softwares, LLC prohíbe una conducta inapropiada que es de naturaleza sexual en el trabajo, en negocios de la empresa o en eventos patrocinados por la compañía, incluidos los siguientes: comentarios, bromas, lenguaje degradante, objetos sexualmente sugerentes, libros o cualquier forma de medios electrónicos o en Formulario de impresión. El acoso sexual está prohibido si se trata entre miembros del sexo opuesto o miembros del mismo sexo. </p>

                <p> 4. Declaración sobre acción afirmativa </p>

                <p> Se ha desarrollado un programa de acción afirmativa donde MI Softwares, LLC busca aumentar la representación y participación de las minorías </p>

                <p> 5. Informar discriminación y acoso </p>

                <p> Si un empleado siente que ha sido hostigado como se describe en esta política, debe presentar inmediatamente la queja con: departamento de quejas, 6255 Towncenter Drive, Ste 819, Clemmons NC 27012, o por correo electrónico en cumplimiento de Softwares. a nosotros. Una vez que se haya informado el asunto, se investigará rápidamente y se tomará cualquier acción correctiva cuando se considere apropiado. Todas las quejas o acoso ilegal bajo esta política o de otra manera se manejarán de la manera más confidencial posible. Se alienta a los informes oportunos a evitar la recurrencia o abordar el comportamiento que viola esta política o ley. Los retrasos al informar una queja pueden limitar el tipo de efectividad de una respuesta por parte de MI Softwares, LLC. El procedimiento para informar incidentes de comportamiento discriminatorio o de acoso no está destinado a evitar el derecho de ningún empleado a buscar un remedio bajo la ley estatal o federal disponible informando inmediatamente el asunto a la agencia estatal o federal apropiada. </p>.

                <p> 6. Retaliación </p>

                <p> Retaliación contra cualquier persona asociada con MI Softwares, LLC, quien informa que los casos de acoso, ya sea que él o ella estén directa o indirectamente involucrados, violan las políticas de MI Softwares, LLC. Se supone que todos los incidentes informados se realizan de buena fe. Cualquier acusación que se demuestre que se tratará como un asunto grave. </p>

                <p> 7. Medidas disciplinarias para el acoso </p>

                <p> Cualquier empleado que participe en el comportamiento que viole esta política estará sujeto a medidas disciplinarias, incluida la posible terminación del empleo, ya sea que se haya violado o no una ley real. </p>.

                <p> 8. Remedios </p>

                <p> remedios para cualquier caso de discriminación del empleo verificada, ya sea causada intencionalmente o por acciones que tengan un efecto discriminatorio, pueden incluir salario posterior, contratación, promoción, restablecimiento, pago frontal, acomodación razonable u otras acciones consideradas apropiadas por los software de MI, LLC. Los recursos también pueden incluir el pago de los honorarios de abogados, honorarios de testigos expertos, costos judiciales y otros honorarios legales aplicables. </p>.

                <p> 9. Implementación de políticas </p>

                <p> mi CEO de Softwares, Lynn Graham, apoya plenamente la implementación de esta política efectiva a partir del 19 de abril de 2021. </p> ',
            'dmv_title' => 'check dmv',
            'dmv' => '<h2> <strong> dmv verificación y consentimiento de verificación de antecedentes </strong> </h2>

                <p> </p>

                <p> Consentimiento para solicitar un registro de conducción </p>

                <P> Entiendo que mi softwares, LLC. ("Compañía") utilizará Checkr. ("Checkr, Inc.") para obtener un registro de vehículos de motor como parte del proceso de solicitud para ser un conductor en la plataforma SoftWares MI (un "conductor"). También entiendo que si se acepta como conductor, en la medida permitida por la ley, la compañía puede obtener más informes de Checkr Inc. para actualizar, renovar o extender mi estado como conductor. Por la presente, doy permiso a MI Softwares para obtener mi registro de conducción estatal (también conocido como mi registro de vehículos motorizados o MVR) de acuerdo con la Ley de Protección de Privacidad Federal del Conductor ("DPPA") y la ley estatal aplicable. Reconozco y entiendo que mi registro de conducción es un informe del consumidor que contiene información de registros públicos. Autorizo, sin reserva, cualquier parte o agencia contactada por Company o Checkr Inc. para proporcionar a la compañía una copia de mi historial de manejo del estado. Esta autorización permanecerá en el archivo de la compañía durante la duración de mi tiempo como conductor, y servirá como autorización continua para que la compañía obtenga mi registro de conducción de estado en cualquier momento mientras yo soy un conductor. </p>

                <p> Consentimiento para solicitar el informe del consumidor o la información del informe del consumidor de investigación </p>

                <P> Entiendo que mi softwares, LLC. ("Compañía") utilizará Checkr Inc., </p>

                <p> 1 Montgomery St, Ste 2000, San Francisco, CA 94104 </p>

                <p> Para obtener un informe del consumidor o un informe de consumo de investigación como parte del proceso de solicitud para ser un impulsor en la plataforma MI SoftWares (un "controlador"). También entiendo que si se acepta como conductor, en la medida permitida por la ley, la compañía puede obtener más informes de la verificación para actualizar, renovar o extender mi estado como conductor. </p>

                <p> Entiendo la investigación de Checkr, Inc ("Checkr") puede incluir la obtención de información sobre mis antecedentes penales, sujeto a cualquier limitaciones impuestas por la ley federal y estatal aplicable. Entiendo que dicha información se puede obtener a través del contacto directo o indirecto con agencias públicas u otras personas que puedan tener dicho conocimiento. </p>.

                <p> La naturaleza y el alcance de la investigación solicitada incluirán una verificación de antecedentes penales y SSN Trace. </p>

                <P> Reconozco la recepción del resumen adjunto de mis derechos bajo la Ley de Informe de Crédito Justo y, según lo exige la ley, cualquier resumen del Estado relacionado de los derechos (colectivamente "Resúmenes de los derechos"). </p>.

                <p> Este consentimiento no afectará mi capacidad de cuestionar o disputar la precisión de cualquier información contenida en un informe. Entiendo que si la compañía toma una decisión condicional de descalificarme basada en todo o en parte en mi informe, me proporcionarán una copia del informe y otra copia de los resúmenes de los derechos, y si no estoy de acuerdo con la precisión de la supuesta descalificación Información En el informe, debo notificar a la compañía dentro de los cinco días hábiles posteriores a la recepción del informe de que estoy desafiando la precisión de dicha información con Checkr. </p>

                <p> Por la presente, consentiré esta investigación y autorizo ​​a la compañía a obtener un informe sobre mis antecedentes. </p>

                <p> Para verificar mi identidad a los efectos de la preparación de los informes, estoy lanzando voluntariamente mi fecha de nacimiento, número de seguro social y la otra información y comprendo completamente que todas las decisiones se basan en razones legítimas no discriminatorias. </P >

                <p> El nombre, la dirección y el número de teléfono de la unidad más cercana de la Agencia de Informes del Consumidor designado para manejar consultas con respecto al Informe del Consumidor de Investigación es: </p>

                <p> <strong> checkr, Inc. <r />
                1 Montgomery St, Ste 2000, San Francisco, CA 94104 <Br />
                844-824-3257 </strong> <r />
                <br />
                <strong> California, Maine, Massachusetts, Minnesota, Nueva Jersey y Oklahoma Solicitantes: </strong> Tengo el derecho de solicitar una copia de cualquier informe obtenido por la compañía de la verificación revisando la casilla. (Verifique solo si desea recibir una copia) </p>

                <p> Solo solicitantes de Nueva York </p>

                <p> También reconozco que he recibido la copia adjunta del Artículo 23A de la Ley de Corrección de Nueva York. Además, entiendo que puedo solicitar una copia de cualquier informe de consumo de investigación contactando a Checkr. Además, entiendo que se me aconsejará si se solicitan más cheques y proporcionan el nombre y la dirección de la Agencia de Informes del Consumidor. </p>

                <p> Solicitantes y residentes de California </p>

                <p> Si estoy solicitando en California o resido en California, entiendo que tengo el derecho de inspeccionar visualmente los archivos sobre mí mantenidos por una agencia de informes de consumidores de investigación durante el horario comercial normal y con un aviso razonable. La inspección se puede hacer en persona y, si parezco en persona y proporciono la identificación adecuada; Tengo derecho a una copia del archivo por una tarifa para no exceder los costos reales de duplicación. Tengo derecho a ir acompañado por una persona de mi elección, que proporcionará una identificación razonable. La inspección también se puede realizar a través del correo certificado si hago una solicitud por escrito, con una identificación adecuada, para que se envíen copias a un destinatario especificado. También puedo solicitar un resumen de la información que se proporcionará por teléfono si hago una solicitud por escrito, con una identificación adecuada para la divulgación del teléfono, y el cargo de peaje, si lo hay, para la llamada telefónica, se me cobra o se me cobra directamente. Además, entiendo que la Agencia de Informes de Consumidores de Investigación proporcionará personal capacitado para explicarme cualquiera de la información proporcionada para mí; Recibiré de la Agencia de Informes de Consumidores de Investigación una explicación por escrito de cualquier información codificada contenida en archivos mantenidos en mí. La "identificación adecuada" como se usa en este párrafo significa que la información generalmente se considera suficiente para identificar a una persona, incluidos documentos como una licencia de conducir válida, número de cuenta de seguro social, tarjeta de identificación militar y tarjetas de crédito. Entiendo que puedo acceder al siguiente sitio web CheckR.com privacidad para ver las prácticas de privacidad de la verificación, incluida la información con respecto a la preparación y el procesamiento de la verificación de los informes y la orientación de los consumidores de investigación sobre si mi información personal se enviará fuera de los Estados Unidos o sus territorios .</p>

                <p> Un resumen de sus derechos bajo la Ley de Informes de Crédito Justo </p>

                <p> La Ley de Informes de Crédito Federal Federal (FCRA) promueve la precisión, la equidad y la privacidad de la información en los archivos de las agencias de informes del consumidor. Existen muchos tipos de agencias de informes del consumidor, incluidas agencias de crédito y agencias especializadas (como agencias que venden información sobre historiales de emisión de cheques, registros médicos y registros de historial de alquiler). Aquí hay un resumen de sus principales derechos bajo la FCRA. <strong> Para obtener más información, incluida la información sobre derechos adicionales, visite www.consumerfinance.gov/learnmore o escriba a: </strong> </p>

                <p> Oficina de protección financiera del consumidor <Br />
                1700 G Street NW, Washington, DC 20552 </p>

                <p> </p>

                <ul>
                <li> Debe decirle si la información en su archivo se ha utilizado en su contra. Cualquier persona que use un informe de crédito u otro tipo de informe del consumidor para negar su solicitud de crédito, seguro o empleo, o para tomar otra acción adversa en su contra, debe decirle y debe darle el nombre, la dirección y el número de teléfono del número de teléfono. la agencia que proporcionó la información. </li>
                <li> Tienes derecho a saber qué hay en tu archivo. Puede solicitar y obtener toda la información sobre usted en los archivos de una agencia de informes del consumidor (su "divulgación de archivos"). Se le pedirá que proporcione una identificación adecuada, que puede incluir su número de Seguro Social. En muchos casos, la divulgación será gratuita. Tiene derecho a una divulgación gratuita de su expediente si:
                <ol>
                <li> Una persona ha tomado medidas adversas contra usted debido a la información en su informe de crédito; </li>
                <li> Eres víctima del robo de identidad y coloca una alerta de fraude en tu archivo; </li>
                <li> Su archivo contiene información inexacta como resultado de fraude; </li>
                <li> Estás en asistencia pública; </li>
                <li> Está desempleado pero espera solicitar empleo dentro de los 60 días. </li>
                </ol>
                Además, todos los consumidores tienen derecho a una divulgación gratuita cada 12 meses previa solicitud de cada agencia de crédito a nivel nacional y de agencias especializadas en informes del consumidor a nivel nacional. Consulte www.consumerfinance.gov/learnmore para obtener información adicional. </li>
                <li> Tienes derecho a pedir un puntaje de crédito. Los puntajes crediticios son resúmenes numéricos de su solvencia crediticia basados ​​en información de las agencias de crédito. Puede solicitar un puntaje crediticio a las agencias de informes del consumidor que crean puntajes o distribuyen puntajes utilizados en préstamos para bienes raíces residenciales, pero tendrá que pagarlo. En algunas transacciones hipotecarias, recibirá información de puntaje de crédito de forma gratuita del prestamista hipotecario. </li>
                <li> Tiene derecho a disputar información incompleta o inexacta. Si identifica información en su archivo que está incompleta o inexacta, e informa a la Agencia de Informes del Consumidor, la agencia debe investigar a menos que su disputa sea frívola. Consulte www.consumerfinance.gov/learnmore para una explicación de los procedimientos de disputa. </li>
                <li> Las agencias de informes del consumidor deben corregir o eliminar información inexacta, incompleta o no verificable. Inaccurate, incomplete or unverifiable information must be removed or corrected, usually within 30 days. However, a consumer reporting agency may continue to report information it has verified as accurate.</li>
                <li>Consumer reporting agencies may not report outdated negative information. In most cases, a consumer reporting agency may not report negative information that is more than seven years old, or bankruptcies that are more than 10 years old.</li>
                <li>Access to your file is limited. A consumer reporting agency may provide information about you only to people with a valid need – usually to consider an application with a creditor, insurer, employer, landlord, or other business. The FCRA specifies those with a valid need for access.</li>
                <li>You must give your consent for reports to be provided to employers. A consumer reporting agency may not give out information about you to your employer, or a potential employer, without your written consent given to the employer. Por lo general, no se requiere consentimiento por escrito en la industria del transporte por carretera. For more information, go to www.consumerfinance.gov/learnmore</li>
                <li>You may limit “prescreened” offers of credit and insurance you get based on information in your credit report. Unsolicited “prescreened” offers for credit and insurance must include a toll-free phone number you can call if you choose to remove your name and address from the lists these offers are based on. You may opt-out with the nationwide credit bureaus at 1-888-567-8688.</li>
                <li>You may seek damages from violators. If a consumer reporting agency, or, in some cases, a user of consumer reports or a furnisher of information to a consumer reporting agency violates the FCRA, you may be able to sue in state or federal court.</li>
                <li>Identity theft victims and active duty military personnel have additional rights. For more information, visit www.consumerfinance.gov/learnmore.</li>
                </ul>

                <p>States may enforce the FCRA, and many states have their own consumer reporting laws. In some cases, you may have more rights under state law. For more information, contact your state or local consumer protection agency or your state Attorney General. For information about your federal rights, contact:</p>

                <p> </p>

                <table>
                <thead>
                <tr>
                <th>
                <p>Type of business</p>
                </th>
                <th>
                <p>Contact</p>
                </th>
                </tr>
                </thead>
                <tcuerpo>
                <tr>
                <td>1.a. Banks, savings associations, and credit unions with total assets of over $10 billion and their affiliates.</td>
                <td>a. Consumer Financial Protection Bureau 1700 G Street NW, Washington, DC 20552</td>
                </tr>
                <tr>
                <td>1.b. Such affiliates that are not banks, savings associations, or credit unions also should list, in addition to the CFPB:</td>
                <td>b. Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td colspan="2">
                <p>To the extent not included in item 1 above</p>
                </td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>2.a. National banks, federal savings associations, and federal branches and federal agencies of foreign banks</td>
                <td>a. Office of the Comptroller of the Currency Customer Assistance Group 1301 McKinney Street Suite 3450, Houston, TX 77010-9050</td>
                </tr>
                <tr>
                <td>2.b. State member banks, branches and agencies of foreign banks (other than federal branches, federal agencies, and Insured State Branches of Foreign Banks), commercial lending companies owned or controlled by foreign banks, and organizations operating under section 25 or 25A of the Federal Reserve Act</td>
                <td>b. Federal Reserve Consumer Help Center P.O. Box 1200 Minneapolis, MN 55480</td>
                </tr>
                <tr>
                <td>2.c. Nonmember Insured Banks, Insured State Branches of Foreign Banks, and insured state savings associations</td>
                <td>c. FDIC Consumer Response Center 1100 Walnut Street Box #11, Kansas City, MO 64106</td>
                </tr>
                <tr>
                <td>2.d. Federal Credit Unions</td>
                <td>d. National Credit Union Administration Office of Consumer Protection (OCP), Division of Consumer Compliance and Outreach (DCCO) 1775 Duke Street, Alexandria, VA 22314</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>3. Air carriers</td>
                <td>Asst. General Counsel for Aviation Enforcement & Proceedings Aviation Consumer Protection Division Department of Transportation 1200 New Jersey Avenue SE, Washington, DC 20590</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>4. Creditors Subject to Surface Transportation Board</td>
                <td>Office of Proceedings, Surface Transportation Board, Department of Transportation 395 E Street SW, Washington, DC 20423</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>5. Creditors Subject to Packers and Stockyards Act, 1921</td>
                <td>Nearest Packers and Stockyards Administration area supervisor</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>6. Small Business Investment Companies</td>
                <td>Associate Deputy Administrator for Capital Access, United States Small Business Administration 409 Third Street SW 8th Floor, Washington, DC 20416</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>7. Brokers and Dealers</td>
                <td>Securities and Exchange Commission 100 F St NE, Washington, DC 20549</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>8. Federal Land Banks, Federal Land Bank Associations, Federal Intermediate Credit Banks, and Production Credit Associations</td>
                <td>Farm Credit Administration, 1501 Farm Credit Drive, McLean, VA 22102-5090</td>
                </tr>
                </tbody>
                <tcuerpo>
                <tr>
                <td>9. Retailers, Finance Companies, and All Other Creditors Not Listed Above</td>
                <td>FTC Regional Office for region in which the creditor operates or Federal Trade Commission: Consumer Response Center – FCRA Washington, DC 20580 877-382-4357</td>
                </tr>
                </tbody>
                </table>',
            'locale' => 'es',
            'language' => 'Spanish',
            'direction' => 'ltr',
        ],
    ]);

    abouts:
    
    $abouts = LandingAbouts::first();

    if($abouts){
        goto end;

    }
    // \Log::info('Seeding LandingAbouts...');
    \DB::table('landing_abouts')->insert([ 
    [
        'id' => Str::uuid(),
        'hero_title' => 'About Us',
        'about_heading'=> 'About',
        'about_title'=> 'The Company',
        'about_para'=> 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
        'about_lists'=> 'Dedicated Team Members,Awesome Services,Customer Support,Quality Assurance',
        'about_img'=> 'company.png',
        'ceo_name'=> 'The CEO',
        'ceo_title'=> 'CEO',
        'ceo_para'=> 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here content here, making it look like readable English',
        'ceo_img'=> 'avatar-4.jpg',
        'signature'=>'signatures.png',
        'vision_mision_heading'=> 'Our Company’s Vision and Mission',
        'vision_title'=> 'Vision',
        'vision_para'=> 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'mission_title'=> 'Mission',
        'mission_para'=> 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_title'=> 'Our Team',
        'team_para'=> 'To achieve this, it would be necessary to have uniform grammar, pronunciation and more common words. If several languages coalesce the grammar.', 
        'team_members' =>  json_encode([
            [
                'team_member_name' => 'Nancy Mart',
                'team_member_posision' => 'Driver',
                'team_member_image' => 'avatar-2.jpg',
            ],
            [
                'team_member_name' => 'John Doe',
                'team_member_posision' => 'Driver',
                'team_member_image' => 'avatar-1.jpg',
            ],
            [
                'team_member_name' => 'Jane Smith',
                'team_member_posision' => 'Driver',
                'team_member_image' => 'avatar-3.jpg',
            ],
            [
                'team_member_name' => 'Nancy Mart',
                'team_member_posision' => 'Driver',
                'team_member_image' => 'avatar-4.jpg',
            ],
        ]),
        'testimonial_content' => json_encode([[
            'testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>',
            'testimonial_title_1' => 'gregoriusus',
            'testimonial_title_2' => 'sample'
        ],[
            'testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>',
            'testimonial_title_1' => 'gregoriusus',
            'testimonial_title_2' => 'sample'
        ],[
            'testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>',
            'testimonial_title_1' => 'gregoriusus',
            'testimonial_title_2' => 'sample'
        ]]),
        'testimonial_heading'=> 'Testimonial Section',
        'locale'=> 'En',
        'language'=> 'English',
        'direction' => 'ltr',
        
    ],
    // arabic
    [
        'id' => Str::uuid(),
        'hero_title' => 'من نحن',
        'about_heading' => 'عن الشركة',
        'about_title' => 'الشركة',
        'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
        'about_lists' => 'أعضاء فريق مخلصين، خدمات رائعة، دعم العملاء، ضمان الجودة',
        'about_img' => 'company.png',
        'ceo_name' => 'المدير التنفيذي',
        'ceo_title' => 'الرئيس التنفيذي',
        'ceo_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'ceo_img' => 'avatar-4.jpg',
        'signature' => 'signatures.png',
        'vision_mision_heading' => 'رؤية ومهمة شركتنا',
        'vision_title' => 'الرؤية',
        'vision_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'mission_title' => 'المهمة',
        'mission_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_title' => 'فريقنا',
        'team_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_members' => json_encode([
            ['team_member_name' => 'نانسي مارت', 'team_member_posision' => 'سائق', 'team_member_image' => 'avatar-2.jpg'],
            ['team_member_name' => 'جون دو', 'team_member_posision' => 'سائق', 'team_member_image' => 'avatar-1.jpg'],
            ['team_member_name' => 'جين سميث', 'team_member_posision' => 'سائق', 'team_member_image' => 'avatar-3.jpg'],
            ['team_member_name' => 'نانسي مارت', 'team_member_posision' => 'سائق', 'team_member_image' => 'avatar-4.jpg'],
        ]),
        'testimonial_content' => json_encode([
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'غريغوريوس', 'testimonial_title_2' => 'نموذج'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'غريغوريوس', 'testimonial_title_2' => 'نموذج'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'غريغوريوس', 'testimonial_title_2' => 'نموذج'],
        ]),
        'testimonial_heading' => 'قسم التوصيات',
        'locale' => 'ar',
        'language' => 'Arabic',
        'direction' => 'rtl',
    ],
    // french
    [
        'id' => Str::uuid(),
        'hero_title' => 'À propos de nous',
        'about_heading' => 'À propos',
        'about_title' => 'L\'entreprise',
        'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
        'about_lists' => 'Membres d\'équipe dédiés, Services exceptionnels, Support client, Assurance qualité',
        'about_img' => 'company.png',
        'ceo_name' => 'Le CEO',
        'ceo_title' => 'CEO',
        'ceo_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'ceo_img' => 'avatar-4.jpg',
        'signature' => 'signatures.png',
        'vision_mision_heading' => 'Vision et Mission de notre entreprise',
        'vision_title' => 'Vision',
        'vision_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'mission_title' => 'Mission',
        'mission_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_title' => 'Notre équipe',
        'team_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_members' => json_encode([
            ['team_member_name' => 'Nancy Mart', 'team_member_posision' => 'Conducteur', 'team_member_image' => 'avatar-2.jpg'],
            ['team_member_name' => 'John Doe', 'team_member_posision' => 'Conducteur', 'team_member_image' => 'avatar-1.jpg'],
            ['team_member_name' => 'Jane Smith', 'team_member_posision' => 'Conducteur', 'team_member_image' => 'avatar-3.jpg'],
            ['team_member_name' => 'Nancy Mart', 'team_member_posision' => 'Conducteur', 'team_member_image' => 'avatar-4.jpg'],
        ]),
        'testimonial_content' => json_encode([
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'gregoriusus', 'testimonial_title_2' => 'exemple'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'gregoriusus', 'testimonial_title_2' => 'exemple'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'gregoriusus', 'testimonial_title_2' => 'exemple'],
        ]),
        'testimonial_heading' => 'Section Témoignages',
        'locale' => 'fr',
        'language' => 'French',
        'direction' => 'ltr',
    ],
    // spanish
    [
        'id' => Str::uuid(),
        'hero_title' => 'Sobre Nosotros',
        'about_heading' => 'Sobre la Compañía',
        'about_title' => 'Compañía',
        'about_para' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage,',
        'about_lists' => 'Miembros del equipo dedicados, Servicios excepcionales, Soporte al cliente, Garantía de calidad',
        'about_img' => 'company.png',
        'ceo_name' => 'El CEO',
        'ceo_title' => 'CEO',
        'ceo_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'ceo_img' => 'avatar-4.jpg',
        'signature' => 'signatures.png',
        'vision_mision_heading' => 'Visión y Misión de nuestra empresa',
        'vision_title' => 'Visión',
        'vision_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'mission_title' => 'Misión',
        'mission_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using Content here, content here making it look like readable English',
        'team_title' => 'Nuestro Equipo',
        'team_para' => 'It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less.',
        'team_members' => json_encode([
            ['team_member_name' => 'Nancy Mart', 'team_member_posision' => 'Conductor', 'team_member_image' => 'avatar-2.jpg'],
            ['team_member_name' => 'John Doe', 'team_member_posision' => 'Conductor', 'team_member_image' => 'avatar-1.jpg'],
            ['team_member_name' => 'Jane Smith', 'team_member_posision' => 'Conductor', 'team_member_image' => 'avatar-3.jpg'],
            ['team_member_name' => 'Nancy Mart', 'team_member_posision' => 'Conductor', 'team_member_image' => 'avatar-4.jpg'],
        ]),
        'testimonial_content' => json_encode([
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'Gregorius', 'testimonial_title_2' => 'ejemplo'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'Gregorius', 'testimonial_title_2' => 'ejemplo'],
            ['testimonial_para' => '<p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Sed perspiciatis commodi voluptas possimus rerum alias eum necessitatibus reiciendis dolorum praesentium aliquid deserunt, consequatur autem delectus eligendi doloribus, eius quos doloremque.</p>', 'testimonial_title_1' => 'Gregorius', 'testimonial_title_2' => 'ejemplo'],
        ]),
        'testimonial_heading' => 'Sección de Testimonios',
        'locale' => 'es',
        'language' => 'Spanish',
        'direction' => 'ltr',
    ]
]);



end:
    }
}