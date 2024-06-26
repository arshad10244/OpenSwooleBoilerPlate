<?php

namespace Shahzaib\Framework\Controllers;

use Monolog\Logger;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Shahzaib\Framework\Core\Factories\ResponseFactory;
use Shahzaib\Framework\Core\Services\AsyncEventService;
use Shahzaib\Framework\EventListeners\LoggerEvents;

class Demo
{


    public function __construct(protected AsyncEventService $asyncEventService, protected Logger $logger)
    {

    }
    public function Index(ServerRequestInterface $request): ResponseInterface
    {
        // dispatch async event
       $this->asyncEventService->dispatchEvent(LoggerEvents::REQUEST,$request);


       //send response
       return ResponseFactory::createResponse(["hello" => "world"]);

    }

    public function protectedPath(ServerRequestInterface $request): ResponseInterface
    {
        // dispatch async event
        $this->asyncEventService->dispatchEvent(LoggerEvents::REQUEST,$request);

        // send response
        return ResponseFactory::createResponse(["Foo" => "Bar","memory"=>memory_get_peak_usage(),"agent"=>$request->getHeader("User-Agent")]);
    }

    public function validateSchema(ServerRequestInterface $request): ResponseInterface
    {

        $body = json_decode($request->getBody(),true);

        // send response
        return ResponseFactory::createResponse(["content" => $body]);
    }



}