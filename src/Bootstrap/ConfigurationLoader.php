<?php


namespace App\Bootstrap;


use Noodlehaus\Config;
use Psr\Container\ContainerInterface;

class ConfigurationLoader
{
    public function load(ContainerInterface $container){
        $container->set('Config', function(){
            return Config::load(realpath('config.json'));
        });
    }
}
