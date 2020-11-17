<?php

namespace App\Providers;

use App\Api\EggInc;
use App\Formatters\EarningBonus;
use App\Formatters\Egg;
use App\Formatters\TimeLeft;
use Illuminate\Support\ServiceProvider;
use RestCord\DiscordClient;

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
        $this->app->singleton(EarningBonus::class);
        $this->app->singleton(Egg::class);
        $this->app->singleton(TimeLeft::class);
        $this->app->bind(DiscordClient::class, function ($app, $options) {
            return new DiscordClient($options);
        });
        $this->app->bind('DiscordClientBot', function ($app) {
            return $app->makeWith(DiscordClient::class, [
                'token'     => config('services.discord.token'),
                'tokenType' => 'Bot',
            ]);
        });
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
