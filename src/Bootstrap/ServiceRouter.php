<?php

namespace App\Bootstrap;
// use App\Middleware\MiddlewareAuth;
use Slim\App;
use App\Middleware\MiddlewareRedirect;
use App\Services\ApiKey;
use App\Services\Authentication;
use App\Services\Health\Health;
use App\Services\EmailCollectionService\EmailCollections;
use App\Services\User\UserService;



class ServiceRouter
{
    /**
     * Prepare service singletons as closures inside DI container
     *
     * @param App $app
     */
    public function load(App $app)
    {
        $app->get('/health', Health::class . ':getHealth');
        $app->get('/emails', EmailCollections::class . ':index')
            ->add(new MiddlewareRedirect());
        $app->get('/authenticate/google', Authentication::class . ':googleAuth');
        $app->get('/authenticate/outlook', Authentication::class . ':outlookAuth');
        $app->post('/create', UserService::class . ':create');
        $app->post('/jwt', ApiKey::class . ':create');
    }
}
