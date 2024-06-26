<?php

namespace Shahzaib\Framework\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Shahzaib\Framework\Core\Factories\ResponseFactory;

class Authentication implements MiddlewareInterface
{

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        /**
         * handle request if scope is not set, this means the endpoint is public
         */
        if(!isset($request->getAttributes()['scope']))
            return $handler->handle($request);


        if(!isset($request->getHeaders()['Authorization']))
            return ResponseFactory::createResponse(["error" => "Authorization header required"], ResponseFactory::UNAUTHORIZED_RESPONSE);

        $token = trim(str_replace("Bearer ", "", $request->getHeaders()['Authorization']));

        try {

            $decodedToken = (array)JWT::decode($token, new Key(JWT_KEY, JWT_ALGORITHM));
        }
        catch (\Exception)
        {
            return ResponseFactory::createResponse(["error" => "Authentication Token Expired or Invalid"], ResponseFactory::UNAUTHORIZED_RESPONSE);
        }

        if(!isset($decodedToken["scope"]) || !in_array($decodedToken['scope'],$request->getAttributes()['scope']))
            return ResponseFactory::createResponse(["error" => "You do not have permissions to view this resource"], ResponseFactory::UNAUTHORIZED_RESPONSE);

    }
}