<?php

namespace DataStorage\Task;


use BusinessLogic\Basis\Content;
use BusinessLogic\Task\ITask;
use BusinessLogic\Task\ITaskRequest;
use DataStorage\Basis\DataHandler;

class TaskHandler extends DataHandler
{
    private $taskAccess = null;

    public function getListPortion(ITaskRequest $taskRequest): Content
    {
        $result = $this->getTaskAccess()->getListPortion($taskRequest)->getData();

        return $result;
    }

    public function search(ITaskRequest $taskRequest): Content
    {
        $result = $this->getTaskAccess()->search($taskRequest)->getData();

        return $result;
    }

    public function countTasks(ITaskRequest $taskRequest): Content
    {
        $result = $this->getTaskAccess()->countTask($taskRequest)->getData();

        return $result;
    }

    private function getTaskAccess(): TaskAccess
    {
        $taskAccess = $this->taskAccess;
        $isExists = !empty($taskAccess);

        if (!$isExists) {
            $access = $this->getAccess();

            switch (DBMS) {
                case SQLITE:
                    $taskAccess = new TaskAccessSqlile($access);
                    break;
            }
            $this->taskAccess = $taskAccess;
        }

        return $taskAccess;
    }

    public function getTheTask(ITaskRequest $taskRequest): Content
    {
        $result = $this->getTaskAccess()->getTheTask($taskRequest)->getData();
        return $result;
    }

    public function registerTask(ITask $task): Content
    {
        $result = $this->getTaskAccess()->insert($task)->getData();

        return $result;
    }

    public function beginTransaction(){
        $this->begin();
    }

    public function finishTransaction(){
        $this->commit();
    }

    public function abortTransaction(){
        $this->rollBack();
    }
}
