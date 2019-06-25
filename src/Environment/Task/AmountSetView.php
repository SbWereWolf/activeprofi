<?php

namespace Environment\Task;


use BusinessLogic\Basis\Content;
use BusinessLogic\Task\ITask;

class AmountSetView implements \Environment\Basis\View
{
    const AMOUNT = 'amount';

    private $dataSet = null;

    public function __construct(Content $dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function toArray(): array
    {
        $collection = array();
        foreach ($this->dataSet->next() as $element) {
            $record = array(
                self::AMOUNT => $element,
            );

            $collection[] = $record;
        }
        return $collection;
    }
}
