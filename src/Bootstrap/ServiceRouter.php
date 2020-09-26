<?php

namespace EmailCollector\Bootstrap;
// use EmailCollector\Middleware\MiddlewareAuth;
use Slim\App;
use EmailCollector\Middleware\MiddlewareRedirect;
use EmailCollector\Services\ApiKey;
use EmailCollector\Services\Authentication;
use EmailCollector\Services\Health\Health;
use EmailCollector\Services\EmailCollectionService\EmailCollections;
use EmailCollector\Services\User\UserService;



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
