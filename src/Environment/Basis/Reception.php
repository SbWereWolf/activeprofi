<?php

namespace Environment\Basis;


use LanguageFeatures\ArrayParser;
use Slim\Http\Request;


class Reception implements IReception
{
    protected $request = null;
    protected $arguments = array();
    protected $parser = null;

    function __construct(Request $request, array $arguments)
    {
        $this->setArguments($arguments)
            ->setRequest($request);
    }

    /**
     * @throws \Exception
     */
    public function toRead()
    {
        throw new \Exception('Method toRead() Not Implemented');
    }

    protected function setParser(ArrayParser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    protected function getParser():ArrayParser
    {
        return $this->parser;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    protected function setRequest(Request $request): Reception
    {
        $this->request = $request;
        return $this;
    }

    protected function getArguments(): array
    {
        return $this->arguments;
    }

    protected function setArguments(array $arguments): Reception
    {
        $this->arguments = $arguments;
        return $this;
    }
}
