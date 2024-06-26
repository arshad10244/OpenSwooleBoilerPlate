<?php

namespace Shahzaib\Framework\Core\Services;

use DI\Container;

final class AsyncEventService
{

    protected array $events = [];
    public function __construct(protected Container $container)
    {

    }

    public function registerEvent(string $event,callable $callback):void
    {

        if(key_exists($event, $this->events))
            throw new \InvalidArgumentException("Duplicate event registration for event: ", $event);

        $this->events[$event] = $callback;
    }

    public function runEvent(string $eventName, string $params):void
    {

        if(key_exists($eventName, $this->events) && is_callable($this->events[$eventName]))
        {
            call_user_func($this->events[$eventName],unserialize($params));
        }

    }

    public function dispatchEvent($name,$data): void
    {
        $this->container->get('events-table')->set(

            rand(99,99999)*time(),
            [
                "event_name" => $name,
                "event_data" => serialize($data),
            ]

        );
    }


}