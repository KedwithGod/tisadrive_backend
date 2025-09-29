<?php

namespace App\Providers;

use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Support\ServiceProvider;

class TranslationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Register a custom path to look for translations
        $this->app->extend('translator', function ($translator, $app) {
            // Add storage/lang to the path
            $loader = $app['translation.loader'];
            $langPath = storage_path('lang');

            // If the lang directory exists in storage, register it
            if (is_dir($langPath)) {
                $loader->addJsonPath($langPath);
            }

            return new Translator($loader, $app['config']['app.locale']);
        });
    }

    public function register()
    {
        //
    }
}
