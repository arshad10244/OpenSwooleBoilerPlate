<?php

namespace Shahzaib\Framework\Core\Contracts;

use DI\Container;
use OpenSwoole\Http\Request;
use OpenSwoole\Http\Response;
use Shahzaib\Framework\Core\Ovverides\MyRouter;

interface Router
{
    public function __construct(MyRouter $router);
}