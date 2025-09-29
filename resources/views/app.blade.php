<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title inertia>{{ app_name() ?? 'Restart' }} - Admin App</title>

    <!-- App favicon -->
    <!-- <link rel="shortcut icon" href="{{ URL::asset('image/favicon.ico') }}"> -->
    <link rel="shortcut icon" id="dynamic-favicon" href="">

 <!-- Firebase SDK -->
<!-- Use the Firebase 8.x version for CommonJS support -->
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-database.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.js.iife.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.0.1/dist/driver.css"/>


    @php
        $default_language = default_language();
    @endphp
    <script>

        window.defaultLocale = "{{ $default_language->code }}";
        window.direction = "{{ $default_language->direction }}";

    </script>
    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

</head>

<body>
    @inertia
    <script>
        window.headers = @json($headers);
        window.recaptchaKey = @json(config('services.recaptcha.site_key'));
        window.enablerecaptcha = @json(config('services.recaptcha.enable_recapcha'));
        window.logo =  @json($logo);
        window.favicon =  @json($favicon);
        window.footer_content1 = @json($footer_content1);
        window.supportTicket = @json($supportTicket);
        window.footer_content2 = @json($footer_content2);
    </script>
</body>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check if window.favicon is available and set it dynamically
        if (window.favicon) {
            document.getElementById('dynamic-favicon').setAttribute('href', window.favicon);
        } else {
            // Fallback if the favicon is not set
            document.getElementById('dynamic-favicon').setAttribute('href', '{{ URL::asset("image/favicon.ico") }}');
        }
    });
</script>
<style>
:root{
    --top_nav: {{ $navs -> value }};
    --side_menu: {{ $side -> value }};
    --side_menu_txt: {{ $side_txt -> value }};
    --loginbg: url('{{ $loginbg }}');
    --owner_loginbg: url('{{ $owner_loginbg }}');
    --landing_header_bg: {{ $landing_header_bg_color -> value }};
    --landing_header_text: {{ $landing_header_text_color -> value }};
    --landing_header_act_text: {{ $landing_header_active_text_color -> value }};
    --landing_footer_bg: {{ $landing_footer_bg_color -> value }};
    --landing_footer_text: {{ $landing_footer_text_color -> value }};
}
</style>

</html>
