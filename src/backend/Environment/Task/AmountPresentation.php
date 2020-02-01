<?php

namespace Environment\Task;


use Environment\Basis\HttpCode;
use Environment\Basis\IPresentation;
use Slim\Http\Response;

class AmountPresentation extends \Environment\Basis\Presentation
{
    /**
     * @return Response
     */
    public function process(): Response
    {
        $shouldAttach = $this->shouldAttach();
        if ($shouldAttach) {
            $this->attachContent();
        }

        $response = (new HttpCode($this->getResponse(), $this->getRequest()))->process($this->isSuccess());

        return $response;
    }

    private function attachContent(): IPresentation
    {
        $asArray = (new AmountSetView($this->getContent()))->toArray();
        $response = $this->getResponse()->withJson($asArray);
        $this->setResponse($response);
        return $this;
    }
}
