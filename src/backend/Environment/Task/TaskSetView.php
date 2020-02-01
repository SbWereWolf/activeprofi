<?php

namespace Environment\Task;


use BusinessLogic\Basis\Content;
use BusinessLogic\Task\ITask;

class TaskSetView implements \Environment\Basis\View
{
    const ID = 'id';
    const TITLE = 'title';
    const DATE = 'date';
    const AUTHOR = 'author';
    const STATUS = 'status';
    const DESCRIPTION = 'description';

    private $dataSet = null;

    public function __construct(Content $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function toArray(): array
    {
        $collection = array();
        foreach ($this->dataSet->next() as $element) {
            /** @var ITask $element */
            $record = array(
                self::ID => $element->getId(),
                self::TITLE => $element->getTitle(),
                self::DATE => $element->getDateString(),
                self::AUTHOR => $element->getAuthor(),
                self::STATUS => $element->getStatus(),
                self::DESCRIPTION => $element->getDescription(),
            );

            $collection[] = $record;
        }
        return $collection;
    }
}
