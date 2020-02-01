<?php

namespace Environment\Basis;


interface Routing
{
    public function getHandler(): \Slim\App;
    public function settingUpRoutes(): Routing;

}
