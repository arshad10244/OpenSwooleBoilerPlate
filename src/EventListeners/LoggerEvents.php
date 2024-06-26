<?php

namespace Shahzaib\Framework\EventListeners;

use DI\Container;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface;
use Shahzaib\Framework\Core\Services\AsyncEventService;

final class LoggerEvents
{
    const REQUEST = 'logger-request';
    const GATEWAY_RESPONSE = 'logger-gateway-response';
    const RESPONSE = 'logger-response';
    const SCHEMA_VALIDATION = 'logger-schema-validation';
    const DEBUG_MESSAGES = 'debug-messages';

    public function __construct(AsyncEventService $asyncEventService, Logger $logger, Container $container)
    {
        $asyncEventService->registerEvent(self::REQUEST,function (ServerRequestInterface $request)use($logger, $container){

            $logger->info("REQUEST RECEIVED",[$request->getAttributes(),$request->getHeaders()]);


        });


        $asyncEventService->registerEvent(self::SCHEMA_VALIDATION,function(bool $result)use($logger){

            if($result)
                $logger->info("VALIDATION PASSED",);
            else
                $logger->alert("VALIDATION FAILED");

        });

        $asyncEventService->registerEvent(self::DEBUG_MESSAGES,function(string $message)use($logger){

            if(DEBUG)
                $logger->debug($message);

        });

    }
}