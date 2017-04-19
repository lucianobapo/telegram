<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (class_exists(\Telegram\Bot\Laravel\TelegramServiceProvider::class))
            $this->app->register(\Telegram\Bot\Laravel\TelegramServiceProvider::class);
        if (class_exists(\Collective\Html\HtmlServiceProvider::class)){
            $this->app->register(\Collective\Html\HtmlServiceProvider::class);
            $this->app->alias('Form', \Collective\Html\FormFacade::class);
        }
    }
}
