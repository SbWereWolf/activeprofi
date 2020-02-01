<?php

namespace Environment\Basis;


use BusinessLogic\Basis\Content;
use Slim\Http\Request;
use Slim\Http\Response;

class Presentation implements IPresentation
{
    /** @var Content $content */
    private $content = null;
    /** @var Response $response */
    private $response = null;
    /** @var Request $request */
    private $request = null;

    public function __construct(Request $request, Response $response, Content $content)
    {
        $this->setContent($content)
            ->setResponse($response)
            ->setRequest($request);
    }
    private function setContent(Content $content): self
    {
        $this->content = $content;
        return $this;
    }

    protected function isSuccess(): bool
    {
        return $this->getContent()->isSuccess();
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @return Response
     * @throws \Exception
     */
    public function process(): Response
    {
        throw new \Exception('Method process() Not Implemented');
    }
    protected function shouldAttach(): bool
    {
        $request = $this->getRequest();
        $shouldAttach = $request->isGet() || $request->isPost();

        return $shouldAttach;
    }

    protected function getContent(): Content
    {
        return $this->content;
    }

    protected function setResponse(Response $response): self
    {
        $this->response = $response;
        return $this;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    private function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }
}
