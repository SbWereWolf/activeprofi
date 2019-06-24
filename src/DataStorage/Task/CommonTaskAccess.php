<?php

namespace DataStorage\Task;


use BusinessLogic\Basis\Content;
use BusinessLogic\Task\Task;
use BusinessLogic\Task\TaskSet;
use DataStorage\Basis\DataAccess;
use LanguageFeatures\ArrayParser;

class CommonTaskAccess extends DataAccess
{
    protected function processForOutput(\PDOStatement $request): self
    {
        $isSuccess = $this->execute($request)->isSuccess();

        $dataSet = array();
        if ($isSuccess) {
            $dataSet = $request->fetchAll(\PDO::FETCH_ASSOC);

            $rowCount = count($dataSet);
            $this->setRowCount($rowCount);
        }

        $shouldParseData = $isSuccess && $this->getRowCount() > 0;
        $data = new TaskSet();
        if ($shouldParseData) {
            $data = $this->parseOutput($dataSet);
        }

        $this->setData($data);

        return $this;
    }

    protected function parseOutput(array $dataSet): Content
    {
        $result = new TaskSet();
        foreach ($dataSet as $dataRow) {
            $parser = new ArrayParser($dataRow);

            $id = $parser->getIntegerField('id');
            $title = $parser->getStringField('title');
            $date = $parser->getStringField('date');
            $author = $parser->getStringField('author');
            $status = $parser->getStringField('status');
            $description = $parser->getStringField('description');

            $item = (new Task())
                ->setId($id)
                ->setTitle($title)
                ->setDateString($date)
                ->setAuthor($author)
                ->setStatus($status)
                ->setDescription($description);

            $result->push($item);
        }

        return $result;
    }

}
