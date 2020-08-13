<?php

namespace App\Providers;

use App\Api\EggInc;
use App\Formatters\Egg;
use App\Formatters\TimeLeft;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(EggInc::class);
        $this->app->singleton(Egg::class);
        $this->app->singleton(TimeLeft::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
