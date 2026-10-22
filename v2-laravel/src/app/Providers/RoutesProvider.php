<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller_rest;
use App\Http\Middleware\ApiKeyAuth;

class RoutesProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(Router $router): void
    {
        Request::macro('validateFormular', function () {
            /** @var Request $this */
            return $this->validate([
                'id'        => 'nullable|int',
                'oid'       => 'nullable|int',
                'firstName' => 'required|string|min:2',
                'lastName'  => 'required|string|min:2',
                'birthdate' => 'nullable|date',
                'email'     => 'required|email',
                'address'   => 'nullable|string',
                'zipCode'   => 'nullable|string',
                'city'      => 'nullable|string',
                'subject'   => 'required|string',
                'message'   => 'nullable|string',
            ]);
        });

        $router
            ->controller(Controller_rest::class)
            ->middleware(ApiKeyAuth::class)
            ->group(function (Router $router) {
                $router->get('/find/{id}', 'find');
                $router->post('/create', 'store');
                $router->post('/update', 'update');
                $router->delete('/delete/{id}', 'destroy');
            });
    }
}