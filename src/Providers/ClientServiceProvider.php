<?php

namespace Spinen\QuickBooks\Providers;

use Session;
use App\Company;
use Spinen\QuickBooks\Client;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

/**
 * Class ClientServiceProvider
 *
 * @package Spinen\QuickBooks
 */
class ClientServiceProvider extends LaravelServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Client::class,
        ];
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function (Application $app) {
            if(request()->route('company')){
                $company = request()->route('company');
            } else {
                $company_id = Session::get('integrated_company');

                $company = Company::where('id', $company_id)->first();
            }

            $token = ($company->quickBooksToken)
                ? : $company->quickBooksToken()
                              ->make();

            return new Client($app->config->get('quickbooks'), $token);
        });

        $this->app->alias(Client::class, 'QuickBooks');
    }
}
