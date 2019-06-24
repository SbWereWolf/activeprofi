<?php

namespace Environment\Task;


use Environment\Basis\Routing;
use Slim\Http\Request;
use Slim\Http\Response;

class Router extends \Environment\Basis\Router
{
    private $root = '/api/v1/task/';

    public function settingUpRoutes(): Routing
    {
        $app = $this->getHandler();
        $dataSource = $this->getDataSource();
        $root = $this->root;
        $app->get($root . 'list/{capacity}/{page}/', function (Request $request, Response $response, array $arguments)
        use ($dataSource) {
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->letSearch()
                ->process();

            return $response;
        });

        $app->get($root . 'list/{sample}/', function (Request $request, Response $response, array $arguments)
        use ($dataSource) {
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->letRetrievePortion()
                ->process();

            return $response;
        });
        $app->get($root . '{id}/', function (Request $request, Response $response, array $arguments)
        use ($dataSource) {
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->letRetrieveTask()
                ->process();

            return $response;
        });

        return $this;
    }
}
