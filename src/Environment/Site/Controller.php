<?php

namespace Environment\Site;


use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class Controller extends \Environment\Basis\Controller
{
    private $viewer = null;

    public function process(): Response
    {
        $request = $this->getRequest();
        $response = $this->getResponse();

        $isGet = $request->isGet();
        if ($isGet) {
            $response = $this->load();
        }

        return $response;
    }

    private function load(): Response
    {
        $viewer = $this->getViewer();
        $response = $this->getResponse();

        $response = $viewer->render($response,
            'Site/index.html');

        return $response;
    }

    /**
     * @return null
     */
    public function getViewer(): PhpRenderer
    {
        return $this->viewer;
    }

    public function setViewer(PhpRenderer $viewer)
    {
        $this->viewer = $viewer;
        return $this;
    }
}
