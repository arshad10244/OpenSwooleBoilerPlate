<?php

namespace Shahzaib\Framework;
use Shahzaib\Framework\Core\Contracts;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
class Registrations implements Contracts\Registrations
{


    /**
     * Classes that require configuration should be added to this array
     * @return array
     */
    public static function getContainerDefinitions(): array
    {
       return [

           // registering Monolog logger

           \Monolog\Logger::class => \DI\factory(function (){

               $logger = new Logger("APP_LOG");
               $logger->pushHandler(new StreamHandler(LOG_PATH));
               return $logger;

           }),

           // add your own registrations here

       ];
    }

}