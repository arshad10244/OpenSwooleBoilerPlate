<?php

namespace Shahzaib\Framework\Core\Ovverides;

use League\Route\RouteConditionHandlerInterface;

final class MyRoute extends \League\Route\Route
{
    protected $scope;

    protected $schema;

    public function setScope(string $scope): self
    {
        $this->scope = $scope;
        return $this;

    }

    public function setSchema(string $schema): self
    {
        $this->schema = $schema;
        return $this;

    }

    public function getScope(): string|null
    {
        return $this->scope;
    }

    public function getSchema(): string|null
    {
        return $this->schema;
    }



}