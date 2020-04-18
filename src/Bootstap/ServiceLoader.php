<?php

namespace EmailCollector\Bootstrap;

use EmailCollector\Helpers\JsonSchemaValidator;
use EmailCollector\Services\Google\GoogleService;
use EmailCollector\Services\User\UserDatabaseInterface;
use Psr\Container\ContainerInterface;

class ServiceLoader
{
    /**
     * Prepare service singletons as closures inside DI container
     *
     * @param ContainerInterface $container
     */
    public function load(ContainerInterface $container)
    {
        $container->set('Google.Service', function(){
            return new GoogleService();
        });

        $container->set('Validator', function(){
            return new JsonSchemaValidator();
        });

        $container->set('UserDatabaseInterface', function() use ($container)
        {
           return new UserDatabaseInterface($container->get('Database.Master'));
        });
    }
}
