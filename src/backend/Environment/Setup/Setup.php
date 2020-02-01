<?php

namespace Environment\Setup;

class Setup
{
    private $handler = null;
    
    public function __construct(\Slim\App $app)
    {
        $this->handler = $app;
    }

    /**
     * @return \Slim\App
     * @throws \Exception
     */
    public function perform(): \Slim\App
    {
        $app = $this->handler;

        $app = (new \Environment\Task\Router($app))->settingUpRoutes()->getHandler();
        $app = (new \Environment\Storage\Router($app))->settingUpRoutes()->getHandler();
        $app = (new \Environment\Site\Router($app))->settingUpRoutes()->getHandler();

        return $app;
    }

}
