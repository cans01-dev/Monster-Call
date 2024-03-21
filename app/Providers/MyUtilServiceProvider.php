<?php

namespace App\Providers;

use App\Libs\MyUtil;
use Illuminate\Support\ServiceProvider;

class MyUtilServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind('MyUtil', MyUtil::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
