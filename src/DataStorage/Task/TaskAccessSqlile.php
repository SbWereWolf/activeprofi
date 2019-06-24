<?php

namespace DataStorage\Task;


use BusinessLogic\Task\ITask;
use BusinessLogic\Task\ITaskRequest;

class TaskAccessSqlile extends CommonTaskAccess implements TaskAccess
{
    public function getListPortion(ITaskRequest $taskRequest): TaskAccess
    {
        $requestText = '
SELECT
    t.id,
    t.title,
    strftime(\'%d.%m.%Y\', t.date, \'unixepoch\') AS date
FROM
    task t
LIMIT :CAPACITY OFFSET (:CAPACITY * :PAGE_NUMBER) 
;
   ';
        $request = $this->prepareRequest($requestText);
        $request->bindValue(':CAPACITY', $taskRequest->getCapacity(), \PDO::PARAM_INT);
        $request->bindValue(':PAGE_NUMBER', $taskRequest->getPage(), \PDO::PARAM_INT);
        $this->processForOutput($request)->processSuccess();

        return $this;
    }

    public function insert(ITask $task): TaskAccess
    {
        $requestText = '
INSERT INTO 
  task
(   
  title,
  date,
  author,
  status,
  description
)
VALUES(
  :TITLE,
  :DATE,
  :AUTHOR,
  :STATUS,
  :DESCRIPTION
)
;
   ';
        $request = $this->prepareRequest($requestText);

        $request->bindValue(':TITLE', $task->getTitle(), \PDO::PARAM_STR);
        $request->bindValue(':DATE', $task->getDateNumber(), \PDO::PARAM_INT);
        $request->bindValue(':AUTHOR', $task->getAuthor(), \PDO::PARAM_STR);
        $request->bindValue(':STATUS', $task->getStatus(), \PDO::PARAM_STR);
        $request->bindValue(':DESCRIPTION', $task->getDescription(), \PDO::PARAM_STR);

        $this->processWrite($request)->processSuccess();

        return $this;
    }

    public function getTheTask(ITaskRequest $taskRequest): TaskAccess
    {
        $requestText = '
SELECT
    t.title,
    strftime(\'%d.%m.%Y\', t.date, \'unixepoch\') AS date,
    t.author,
    t.status,
    t.description 
FROM
    task t
WHERE
  t.id = :ID
;
   ';
        $request = $this->prepareRequest($requestText);
        $request->bindValue(':ID', $taskRequest->getId(),\PDO::PARAM_INT);
        $this->processForOutput($request)->processSuccess();

        return $this;
    }

    public function search(ITaskRequest $taskRequest): TaskAccess
    {
        // TODO: fulltext search ?
        $requestText = '
SELECT
    t.id,
    t.title,
    strftime(\'%d.%m.%Y\', t.date, \'unixepoch\') AS date
FROM
    task t
WHERE
  t.title LIKE \'%\' || :SAMPLE || \'%\'
  OR t.author  LIKE \'%\' || :SAMPLE || \'%\'
  OR t.description  LIKE \'%\' || :SAMPLE || \'%\'
;
   ';
        $request = $this->prepareRequest($requestText);
        $request->bindValue(':SAMPLE', $taskRequest->getId(),\PDO::PARAM_INT);
        $this->processForOutput($request)->processSuccess();

        return $this;
    }
}
