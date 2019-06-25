<?php

namespace BusinessLogic\Task;


use BusinessLogic\Basis\Content;
use DataStorage\Basis\DataSource;

class TaskProcess
{
    private $taskRequest = null;

    public function __construct(ITaskRequest $taskRequest)
    {
        $this->setTaskRequest($taskRequest);
    }

    public function getTaskRequest(): ITaskRequest
    {
        return $this->taskRequest;
    }

    public function setTaskRequest(ITaskRequest $taskRequest): self
    {
        $this->taskRequest = $taskRequest;
        return $this;
    }

    public function retrieveTask(DataSource $dataPath): Content
    {
        $taskSet = $taskSet = (new TaskManager($this->getTaskRequest(), $dataPath))->getTheTask();

        return $taskSet;
    }

    public function retrievePortion(DataSource $dataPath): Content
    {
        $taskSet = (new TaskManager($this->getTaskRequest(), $dataPath))->getListPortion();

        return $taskSet;
    }

    public function search(DataSource $dataPath): Content
    {
        $taskSet = (new TaskManager($this->getTaskRequest(), $dataPath))->search();

        return $taskSet;
    }

    public function countTasks(DataSource $dataPath): Content
    {
        $amountSet = (new TaskManager($this->getTaskRequest(), $dataPath))->countTasks();

        return $amountSet;
    }
}
