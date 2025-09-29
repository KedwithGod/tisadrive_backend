<?php

namespace App\Providers;

use App\Base\Services\Hash\HashGenerator;
use App\Base\Services\Hash\HashGeneratorContract;
use App\Base\Services\OTP\Generator\OTPGenerator;
use App\Base\Services\OTP\Generator\OTPGeneratorContract;
use App\Base\Services\OTP\Handler\DatabaseOTPHandler;
use App\Base\Services\OTP\Handler\OTPHandlerContract;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class MIServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMorphMaps();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(HashGeneratorContract::class, HashGenerator::class);

        
        $this->app->singleton(OTPGeneratorContract::class, OTPGenerator::class);
        $this->app->singleton(OTPHandlerContract::class, DatabaseOTPHandler::class);

    }

    /**
     * Custom model relationship morph maps.
     *
     * @return void
     */
    protected function registerMorphMaps()
    {
        // Relation::morphMap([
        //     'Vendor' => 'App\Models\Admin\Merchants'
        // ]);
    }
}
