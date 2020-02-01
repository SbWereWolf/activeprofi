<?php

namespace Environment\Basis;


use DataStorage\Basis\DataSource;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller implements IController
{
    private $request = null;
    private $response = null;
    private $arguments = array();
    private $dataPath = null;

    function __construct(Request $request, Response $response, array $parametersInPath, DataSource $dataPath)
    {
        $this->setArguments($parametersInPath)
            ->setDataPath($dataPath)
            ->setRequest($request)
            ->setResponse($response);
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function process(): Response
    {
        throw new \Exception('Method process() Not Implemented');
    }

    protected function setResponse(Response $response): Controller
    {
        $this->response = $response;
        return $this;
    }

    protected function getResponse(): Response
    {
        return $this->response;
    }

    protected function setArguments(array $arguments): Controller
    {
        $this->arguments = $arguments;
        return $this;
    }

    protected function getArguments(): array
    {
        return $this->arguments;
    }

    protected function setDataPath(DataSource $dataPath): Controller
    {
        $this->dataPath = $dataPath;
        return $this;
    }

    protected function getDataPath(): DataSource
    {
        return $this->dataPath;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    protected function setRequest(Request $request): Controller
    {
        $this->request = $request;
        return $this;
    }
}
