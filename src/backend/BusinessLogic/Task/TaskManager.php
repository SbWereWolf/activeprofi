<?php

namespace BusinessLogic\Task;


use BusinessLogic\Basis\Content;
use DataStorage\Basis\DataSource;
use DataStorage\Task\TaskHandler;

class TaskManager
{
    private $taskRequest = null;
    private $dataPath = null;

    public function __construct(ITaskRequest $taskRequest, DataSource $dataPath)
    {
        $this->setDataPath($dataPath)
            ->setTaskRequest($taskRequest);
    }

    public function setTaskRequest(ITaskRequest $taskRequest): self
    {
        $this->taskRequest = $taskRequest;
        return $this;
    }


    public function getTaskRequest(): ITaskRequest
    {
        return $this->taskRequest;
    }

    public function setDataPath(DataSource $dataPath): self
    {
        $this->dataPath = $dataPath;
        return $this;
    }

    public function getDataPath(): DataSource
    {
        return $this->dataPath;
    }

    public function getListPortion(): Content
    {
        $taskRequest = $this->getTaskRequest();
        $dataPath = $this->getDataPath();

        $result = (new TaskHandler($dataPath))->getListPortion($taskRequest);

        return $result;
    }

    public function getTheTask(): Content
    {
        $taskRequest = $this->getTaskRequest();
        $dataPath = $this->getDataPath();

        $result = (new TaskHandler($dataPath))->getTheTask($taskRequest);

        return $result;
    }

    public function search(): Content
    {
        $taskRequest = $this->getTaskRequest();
        $dataPath = $this->getDataPath();

        $result = (new TaskHandler($dataPath))->search($taskRequest);

        return $result;
    }

    public function countTasks(): Content
    {
        $taskRequest = $this->getTaskRequest();
        $dataPath = $this->getDataPath();

        $result = (new TaskHandler($dataPath))->countTasks($taskRequest);

        return $result;
    }
}
