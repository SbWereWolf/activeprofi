<?php

namespace Environment\Storage;


use Environment\Basis\Routing;
use Slim\Http\Request;
use Slim\Http\Response;

class Router extends \Environment\Basis\Router
{
    private $root = '/api/v1/storage';

    public function settingUpRoutes(): Routing
    {
        $app = $this->getHandler();
        $dataSource = $this->getDataSource();
        $root = $this->root;
        $app->post("$root/install/", function (Request $request, Response $response, array $arguments)
        use ($dataSource) {
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->process();

            return $response;
        });
        $app->delete("$root/dismount/", function (Request $request, Response $response, array $arguments)
        use ($dataSource) {
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->process();

            return $response;
        });

        return $this;
    }
}
