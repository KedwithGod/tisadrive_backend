<?php

namespace App\Providers;
use App\Base\Services\Setting\UpdateSettingContract;
use App\Base\Services\Setting\UpdateSetting;

use Illuminate\Support\ServiceProvider;

class UpdateServiceProvider extends ServiceProvider
{
    /**
     * Register the application services.
     */
    public function register() {
        $this->registerAccess(); 
    }

    /**
     * Register the application bindings.
     */
    protected function registerAccess() {
        
        $this->app->bind('update-service', UpdateSetting::class);

        $this->app->alias('update-service', UpdateSettingContract::class);
    }

   
}
