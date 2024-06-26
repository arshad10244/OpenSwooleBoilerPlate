<?php

namespace Shahzaib\Framework\Core\Ovverides;


use League\Route\Dispatcher;
use League\Route\RouteConditionHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;

final class MyRouter extends \League\Route\Router
{


    public function map(string $method, string $path, $handler): MyRoute
    {
        $path  = sprintf('/%s', ltrim($path, '/'));
        $route = new MyRoute($method, $path, $handler);

        $this->routes[] = $route;

        return $route;
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        if (false === $this->routesPrepared) {
            $this->prepareRoutes($request);
        }

        /** @var Dispatcher $dispatcher */
        $dispatcher = (new MyDispatcher($this->routesData))->setStrategy($this->getStrategy());

        foreach ($this->getMiddlewareStack() as $middleware) {
            if (is_string($middleware)) {
                $dispatcher->lazyMiddleware($middleware);
                continue;
            }

            $dispatcher->middleware($middleware);
        }

        return $dispatcher->dispatchRequest($request);
    }

}