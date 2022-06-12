<?php


namespace App\Bootstrap;

// use Monolog\Logger;
// use Monolog\Formatter\LineFormatter;
// use Monolog\Handler\SyslogUdpHandler;
// use Psr\Container\ContainerInterface;

class ServiceLogger
{

    //    public function load(ContainerInterface $container)
    //    {
    //        $container['logger'] = function() {
    //            // Set the format
    //            $output = "%channel%.%level_name%: %message%";
    //            $formatter = new LineFormatter($output);
    //
    //            // Setup the logger
    //            $logger = new Logger('email-collector');
    //            $syslogHandler = new SyslogUdpHandler("logs3.papertrailapp.com", 51951);
    //            $syslogHandler->setFormatter($formatter);
    //            $logger->pushHandler($syslogHandler);
    //        };
    //
    //        return $container;
    //    }
}
