<?php

namespace EmailCollector;

use DI\Container;
use EmailCollector\Bootstrap\ConfigurationLoader;
use EmailCollector\Bootstrap\ServiceDatabase;
use EmailCollector\Bootstrap\ServiceRouter;
use EmailCollector\Bootstrap\ServiceLoader;
use Psr\Container\ContainerInterface;
use Slim\Factory\AppFactory;

ini_set('xdebug.overload_var_dump', 0);

class ApplicationFactory
{
    public function make(){

        // we set the DI container
        $container = new Container();

        // we pass it to the slim factory so that it registers
        AppFactory::setContainer($container);

        // initialize the application
        $app = AppFactory::create();

        // load everything before executing the application
        $this->loadRoutes($app);
        $this->loadServices($container);
        $this->loadConfiguration($container);
        $this->loadDatabases($container);

        $app->addErrorMiddleware(true, false, false);
        $app->run();
    }

    /**
     * Loading all services for the application
     * @param $container ContainerInterface
     */
    private function loadServices($container)
    {
        $services = new ServiceLoader();
        return $services->load($container);
    }

    /**
     * Loading all routes for the application
     * @param $app Slim
     */
    private function loadRoutes($app)
    {
        $services = new ServiceRouter();
        return $services->load($app);
    }

    /**
     * Loading all databases for the application
     * @param $container ContainerInterface
     * @return ServiceDatabase
     */
    private function loadDatabases($container)
    {
        $services = new ServiceDatabase();
        return $services->load($container);
    }

    private function loadConfiguration($container){
        $service = new ConfigurationLoader();
        return $service->load($container);
}

}
