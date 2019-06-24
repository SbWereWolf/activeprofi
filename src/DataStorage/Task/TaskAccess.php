<?php

namespace DataStorage\Task;


use BusinessLogic\Task\ITask;
use BusinessLogic\Task\ITaskRequest;
use DataStorage\Basis\IDataAccess;

interface TaskAccess extends IDataAccess
{
    public function getListPortion(ITaskRequest $taskRequest): self;

    public function insert(ITask $task): self;

    public function getTheTask(ITaskRequest $taskRequest): self;

    public function search(ITaskRequest $taskRequest): self;
}
