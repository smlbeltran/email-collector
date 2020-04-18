<?php

namespace EmailCollector\Bootstrap;

use EmailCollector\Middleware\MiddlewareAuth;

use EmailCollector\Service\Gmail\GmailService;
use EmailCollector\Services\ApiKey;
use EmailCollector\Services\Google\GoogleService;
use EmailCollector\Services\Outlook\OutlookService;
use EmailCollector\Services\User\UserService;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

class ServiceRouter
{
    /**
     * Prepare service singletons as closures inside DI container
     *
     * @param App $app
     */
    public function load(App $app)
    {
        $app->group('', function(RouteCollectorProxy $group){
            $group->get('/emails', GmailService::class . ':index');
            $group->get('/auth', GoogleService::class . ':auth');

        })->add(new MiddlewareAuth());

        $app->get('/outlook_auth', OutlookService::class . ':auth');
        $app->get('/outlook_test', OutlookService::class . ':example');
        $app->post('/create', UserService::class . ':create');
        $app->post('/jwt', ApiKey::class . ':create');
    }
}
