<?php

namespace Shahzaib\Framework\Core\Middlewares;



use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shahzaib\Framework\Core\Factories\ResponseFactory;
use Shahzaib\Framework\Core\Services\AsyncEventService;
use Shahzaib\Framework\EventListeners\LoggerEvents;


final class SchemaValidator implements MiddlewareInterface
{

    public function __construct(protected AsyncEventService $eventService, protected Validator $validator, protected ErrorFormatter $errorFormatter)
    {

    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

            // Log
            $this->eventService->dispatchEvent(LoggerEvents::DEBUG_MESSAGES,"Schema Validation Middleware Triggered");

            // If request method is not one of schemas, handle the request
            if(!in_array($request->getMethod(), SCHEMA_VALIDATION_RQST_METHODS))
                return $handler->handle($request);

            // if schema is not set but force schema is true, throw error
            if(!isset($request->getAttributes()['schema']) && FORCE_RQST_SCHEMA)
                return ResponseFactory::createResponse(["error"=>"Schema validation requires schema"],ResponseFactory::BAD_REQUEST_RESPONSE);

            // if schema is not set and force schema is false, handle the request
            if(!isset($request->getAttributes()['schema']) && !FORCE_RQST_SCHEMA)
                return $handler->handle($request);

            try {
                // validate request
                $validationResult = $this->validator->validate(json_decode($request->getBody()),SCHEMA_PREFIX.'/'.$request->getAttributes()['schema'].'.json');
            }
            catch (\RuntimeException $exception)
            {
                // schema json does not exist, throw error
                return ResponseFactory::createResponse([
                    "error"=>$exception->getMessage()
                ], ResponseFactory::INTERNAL_SERVER_ERROR_RESPONSE);
            }

            // if validation is successful, handle the request
            if($validationResult->isValid())
                return $handler->handle($request);


            // throw validation errors
            return ResponseFactory::createResponse(["validation_errors" => $this->errorFormatter->formatNested($validationResult->error())],ResponseFactory::BAD_REQUEST_RESPONSE);


    }
}