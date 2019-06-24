<?php

namespace Environment\Basis;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

class HttpCode extends StatusCode implements IHttpCode
{

    private $response = null;
    private $request = null;
    private $shouldTransformFailToNoData = false;

    function __construct(Response $response, Request $request)
    {
        $this->setRequest($request)
            ->setResponse($response);
    }

    private function setResponse(Response $response): HttpCode
    {
        $this->response = $response;
        return $this;
    }

    private function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function process(bool $isSuccess): Response
    {
        $status = $this->calculateStatus($isSuccess);
        $response = $this->getResponse()->withStatus($status);

        return $response;
    }

    private function getRequest(): Request
    {
        return $this->request;
    }

    private function calculateStatus(bool $isSuccess): int
    {
        $statusCode = self::HTTP_IM_A_TEAPOT;

        if (!$isSuccess) {
            $statusCode = self::HTTP_INTERNAL_SERVER_ERROR;
        }

        $request = $this->getRequest();

        $isGet = $request->isGet();
        if ($isGet && $isSuccess) {
            $statusCode = self::HTTP_OK;
        }

        $shouldTransformFailToNotFound = $this->isTransformFailToNotFound();
        if ($isGet && !$isSuccess && $shouldTransformFailToNotFound) {
            $statusCode = self::HTTP_NOT_FOUND;
        }

        $isDelete = $request->isDelete();
        if ($isSuccess && $isDelete) {
            $statusCode = self::HTTP_NO_CONTENT;
        }
        $isPost = $request->isPost();
        if ($isSuccess && $isPost) {
            $statusCode = self::HTTP_CREATED;
        }
        $isPut = $request->isPut();
        if ($isSuccess && $isPut) {
            $statusCode = self::HTTP_OK;
        }

        return $statusCode;
    }

    private function getResponse(): Response
    {
        return $this->response;
    }

    public function letTransformFailToNotFound(): self
    {
        $this->shouldTransformFailToNoData = true;
        return $this;
    }

    private function isTransformFailToNotFound(): bool
    {
        return $this->shouldTransformFailToNoData;
    }

}
