<?php

namespace App\Bootstrap;

use App\Helpers\JsonSchemaValidator;
use App\Services\User\UserDatabaseInterface;
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
        $container->set('Validator', function () {
            return new JsonSchemaValidator();
        });

        $container->set('UserDatabaseInterface', function () use ($container) {
            return new UserDatabaseInterface($container->get('Database.Master'));
        });
    }
}
