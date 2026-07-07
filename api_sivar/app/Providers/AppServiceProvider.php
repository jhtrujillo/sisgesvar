<?php

namespace App\Providers;

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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // C-5: impedir despliegues con APP_DEBUG=true en producción
        // (evita fugas de credenciales y trazas por la página de error de Ignition).
        if ($this->app->environment('production') && config('app.debug')) {
            throw new \RuntimeException(
                'APP_DEBUG debe ser false en producción. Revise el .env del entorno.'
            );
        }
    }
}
