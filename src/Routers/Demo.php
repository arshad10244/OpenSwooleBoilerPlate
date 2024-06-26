<?php

namespace Shahzaib\Framework\Routers;

use Shahzaib\Framework\Core\Contracts\Router;
use Shahzaib\Framework\Core\Ovverides\MyRouter;
use Shahzaib\Framework\Middlewares\Authentication;

class Demo implements Router
{
    public function __construct(MyRouter $router)
    {


        /**
         * Index page without scope or schema
         */
        $router->map('GET', '/', [\Shahzaib\Framework\Controllers\Demo::class,'Index']);


        /**
         * Post Request, validated against json schema
         * @see /src/Schemas/dummy_schema.json
         */
        $router->map('POST','/validate_schema', [\Shahzaib\Framework\Controllers\Demo::class,'validateSchema'])->setSchema("dummy_schema");


        /**
         * Protected GET request
         * Scope needed is : view.protected
         * @note scopes are searched in JWT token within 'scopes' array
         * @see Authentication
         */
        $router->map('POST','/protected', [\Shahzaib\Framework\Controllers\Demo::class,'protectedPath'])->setScope('view.protected');





    }
}