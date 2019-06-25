<?php

namespace Environment\Site;


use Environment\Basis\Routing;
use Slim\Http\Request;
use Slim\Http\Response;

class Router extends \Environment\Basis\Router
{
    private $root = '/';

    public function settingUpRoutes(): Routing
    {
        $app = $this->getHandler();
        $dataSource = $this->getDataSource();
        $root = $this->root;
        $app->get("$root", function (Request $request, Response $response, array $arguments)
        use ($dataSource) {

            $viewer = $this->get(VIEW_RENDERER);
            $response = (new Controller($request, $response, $arguments, $dataSource))
                ->setViewer($viewer)
                ->process();

            return $response;
        });

        return $this;
    }
}
