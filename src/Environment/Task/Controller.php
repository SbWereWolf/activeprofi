<?php

namespace Environment\Task;


use BusinessLogic\Task\TaskProcess;
use BusinessLogic\Task\TaskRequest;
use Slim\Http\Response;

class Controller extends \Environment\Basis\Controller
{
    private $shouldRetrievePortion = false;
    private $shouldRetrieveTask = false;
    private $shouldSearch = false;
    private $shouldCount = false;

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

        $letCount = $isGet && $this->isCount();
        if ($letCount) {
            $response = $this->countTasks($reception);
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

    private function countTasks(Reception $reception): Response
    {
        $dataPath = $this->getDataPath();

        $amountSet = (new TaskProcess(new TaskRequest()))->countTasks($dataPath);

        $response = (new AmountPresentation($this->getRequest(), $this->getResponse(), $amountSet))->process();

        return $response;
    }

    public function letRetrievePortion(): self
    {
        $this->shouldRetrievePortion = true;

        return $this;
    }

    private function isRetrievePortion(): bool
    {
        return $this->shouldRetrievePortion;
    }

    public function letRetrieveTask(): self
    {
        $this->shouldRetrieveTask = true;

        return $this;
    }

    private function isRetrieveTask(): bool
    {
        return $this->shouldRetrieveTask;
    }

    public function letSearch(): self
    {
        $this->shouldSearch = true;

        return $this;
    }

    private function isSearch(): bool
    {
        return $this->shouldSearch;
    }

    public function letCount(): self
    {
        $this->shouldCount = true;

        return $this;
    }

    private function isCount(): bool
    {
        return $this->shouldCount;
    }
}
