<?php

namespace Environment\Task;


use BusinessLogic\Task\TaskProcess;
use Slim\Http\Response;

class Controller extends \Environment\Basis\Controller
{
    private $shouldRetrievePortion = false;
    private $shouldRetrieveTask = false;
    private $shouldSearch = false;

    public function process(): Response
    {
        $request = $this->getRequest();

        $isGet = $request->isGet();

        $isValid = $isGet;
        $reception = null;
        if ($isValid) {
            $arguments = $this->getArguments();
            $reception = new  Reception($request, $arguments);
        }

        $response = $this->getResponse();

        $letRetrievePortion = $isGet && $this->isRetrievePortion();
        if ($letRetrievePortion) {
            $response = $this->retrievePortion($reception);
        }

        $letRetrieveTask = $isGet && $this->isRetrieveTask();
        if ($letRetrieveTask) {
            $response = $this->retrieveTask($reception);
        }

        $letSearch = $isGet && $this->isSearch();
        if ($letSearch) {
            $response = $this->search($reception);
        }

        return $response;
    }

    private function retrievePortion(Reception $reception): Response
    {
        $item = $reception->toRead();

        $dataPath = $this->getDataPath();
        $taskSet = (new TaskProcess($item))->retrievePortion($dataPath);

        $response = (new Presentation($this->getRequest(), $this->getResponse(), $taskSet))->process();

        return $response;
    }

    private function retrieveTask(Reception $reception): Response
    {
        $item = $reception->toRead();

        $dataPath = $this->getDataPath();

        $taskSet = (new TaskProcess($item))->retrieveTask($dataPath);

        $response = (new Presentation($this->getRequest(), $this->getResponse(), $taskSet))->process();

        return $response;
    }

    private function search(Reception $reception): Response
    {
        $item = $reception->toRead();

        $dataPath = $this->getDataPath();

        $taskSet = (new TaskProcess($item))->search($dataPath);

        $response = (new Presentation($this->getRequest(), $this->getResponse(), $taskSet))->process();

        return $response;
    }

    public function letRetrievePortion(): self
    {
        $this->shouldRetrievePortion = true;

        return $this;
    }

    public function letRetrieveTask(): self
    {
        $this->shouldRetrieveTask = true;

        return $this;
    }

    public function letSearch(): self
    {
        $this->shouldSearch = true;

        return $this;
    }

    private function isRetrievePortion(): bool
    {
        return $this->shouldRetrievePortion;
    }

    private function isRetrieveTask(): bool
    {
        return $this->shouldRetrieveTask;
    }

    private function isSearch(): bool
    {
        return $this->shouldSearch;
    }
}
