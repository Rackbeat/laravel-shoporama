<?php

namespace KgBot\Shoporama\;


use Illuminate\Support\ServiceProvider;

class ShoporamaServiceProvider extends ServiceProvider
{
    /**
     * Boot.
     */
    public function boot()
    {
        $configPath = __DIR__ . '/config/shoporama.php';

        $this->mergeConfigFrom($configPath, 'shoporama');

        $configPath = __DIR__ . '/config/shoporama.php';

        if (function_exists('config_path')) {

            $publishPath = config_path('shoporama.php');

        } else {

            $publishPath = base_path('config/shoporama.php');

        }

        $this->publishes([$configPath => $publishPath], 'config');
    }

    public function register()
    {
    }
}