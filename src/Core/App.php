<?php

namespace Shahzaib\Framework\Core;

use DI\Container;
use League\Route\Strategy\ApplicationStrategy;
use OpenSwoole\Http\Server;
use OpenSwoole\Table;
use OpenSwoole\Timer;
use Psr\Http\Message\ServerRequestInterface;
use Shahzaib\Framework\Core\Factories\ResponseFactory;
use Shahzaib\Framework\Core\Middlewares\SchemaValidator;
use Shahzaib\Framework\Core\Ovverides\MyRouter;
use Shahzaib\Framework\Core\Services\AsyncEventService;
use Shahzaib\Framework\Core\Services\MakeClass;
use Shahzaib\Framework\Registrations;
use DI\ContainerBuilder;
use Opis\JsonSchema\Validator;


final class App
{

    protected Container $container;
    protected Server $server;
    protected MyRouter $router;



    public function configureApp():void
    {
        echo "- Building Container".PHP_EOL;
        $this->buildContainer();

        $this->server = $this->container->get(Server::class);
        $this->router = $this->container->get(MyRouter::class);

        echo "- Registering Middlewares".PHP_EOL;
        $this->registerMiddlewares();

        echo "- Registering Routers".PHP_EOL;
        $this->registerRouters();

        echo "- Creating Events Table".PHP_EOL;
        $this->createEventsTable();

        echo "- Starting Events Processor".PHP_EOL;
        $this->startEventsProcessor();

        echo "- Registering Event Listeners".PHP_EOL;
        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        $this->container->get(MakeClass::class)->makeDirectory(ROOT.'/src/EventListeners','Shahzaib\Framework\EventListeners');
    }


    private function startEventsProcessor():void
    {
        $eventsTable = $this->container->get('events-table');
        $eventService = $this->container->get(AsyncEventService::class);

        Timer::tick(ASYNC_TASK_TIMER,function ()use($eventsTable,$eventService){

            foreach($eventsTable as $key=>$row)
            {
                $eventService->runEvent($row['event_name'],$row['event_data']);
                $eventsTable->del($key);
            }

        });
    }

    private function createEventsTable():void
    {
       $this->container->set('events-table',function (){

           $table = new Table(ASYNC_TASK_MEMORY_LIMIT);
           $table->column("event_name",Table::TYPE_STRING,50);
           $table->column("event_data",Table::TYPE_STRING,ASYNC_TASK_DATA_SIZE);
           $table->create();
           return $table;

       });

    }

    private function registerMiddlewares():void
    {
       $this->router->middleware($this->container->get(SchemaValidator::class));

       $middleWares = $this->container->get(MakeClass::class)->getClassesFromDirectory(ROOT.'/src/Middlewares','Shahzaib\Framework\Middlewares');

       foreach($middleWares as $class)
           $this->router->middleware($this->container->get($class));

       $strategy = (new ApplicationStrategy())->setContainer($this->container);
       $this->router->setStrategy($strategy);
    }


    private function buildContainer(): void
    {
        $container = new ContainerBuilder();

        // load user defined definitions
        $definitions = Registrations::getContainerDefinitions();

        // add server definition
        $definitions[Server::class] = \DI\factory(function () {
            return new Server(\SERVER['bind'], \SERVER['port']);
        });

        //add opis schema definition

        $definitions[Validator::class] = \DI\factory(function (){

            $validator = new Validator();
            $validator->resolver()->registerPrefix('https://api.example.com/',ROOT.'/src/Schemas/');
            $validator->setMaxErrors(20);
            return $validator;

        });



        $container->addDefinitions($definitions);


        try {

            $this->container = $container->build();

            //cleanup memory
            unset($definitions);

        } catch (\Exception $e) {

            print_r($e->getMessage());
            exit();

        }
    }


    private function registerRouters(): void
    {
        $this->container->get(MakeClass::class)->makeDirectory(ROOT.'/src/Routers','Shahzaib\Framework\Routers');
    }



    public function startServer()
    {
        $this->server->on("start",function (Server $server){
            echo "- Server Started on interface: ".\SERVER['bind'].", Port: ".\SERVER['port'].PHP_EOL;
        });


            $this->server->handle(function (ServerRequestInterface $request) {

                try {
                    return $this->router->dispatch($request);
                } catch (\Exception $exception)
                {
                    if($request->getMethod() === "GET" && $exception->getCode() === 0)
                    return ResponseFactory::createResponse(["error"=>"Resource Not Found"],ResponseFactory::NOT_FOUND_RESPONSE);
                    else if($exception->getCode() !== 0)
                        return ResponseFactory::createResponse(["error"=>$exception->getMessage()],ResponseFactory::INTERNAL_SERVER_ERROR_RESPONSE);
                    else
                        return ResponseFactory::createResponse([],ResponseFactory::NOT_IMPLEMENTED_RESPONSE);

                }

            });


        $this->server->start();
    }

}