<?php

namespace Shahzaib\Framework\Core\Ovverides;

use FastRoute\Dispatcher as FastRoute;
use League\Route\Dispatcher;
use League\Route\Route;
use OpenSwoole\Server;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class MyDispatcher extends Dispatcher
{

    public function dispatchRequest(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        $uri    = $request->getUri()->getPath();
        $match  = $this->dispatch($method, $uri);

        switch ($match[0]) {
            case FastRoute::NOT_FOUND:
                $this->setNotFoundDecoratorMiddleware();
                break;
            case FastRoute::METHOD_NOT_ALLOWED:
                $allowed = (array) $match[1];
                $this->setMethodNotAllowedDecoratorMiddleware($allowed);
                break;
            case FastRoute::FOUND:
                $route = $this->ensureHandlerIsRoute($match[1], $method, $uri)->setVars($match[2]);

                $request = $this->addScopeToRequest($request, $route);
                $request = $this->addSchemaToRequest($request, $route);

                if ($this->isExtraConditionMatch($route, $request)) {
                    $this->setFoundMiddleware($route);
                    $request = $this->requestWithRouteAttributes($request, $route);
                    break;
                }

                $this->setNotFoundDecoratorMiddleware();
                break;
        }

        return $this->handle($request);
    }

    protected function addScopeToRequest(ServerRequestInterface $request, $route): ServerRequestInterface
    {

        return $request->withAttribute("scope",$route->getScope());

    }

    function addSchemaToRequest(ServerRequestInterface $request, $route): ServerRequestInterface
    {
        return $request->withAttribute("schema",$route->getSchema());
    }
}