<?php

namespace App\Providers;

use App\Contracts\File;
use App\Services\File\Disk;
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
        $this->app->bind(File::class, function ($app) {
            return new Disk();
        });
    }
}
