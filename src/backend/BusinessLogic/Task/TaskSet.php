<?php

namespace BusinessLogic\Task;


use BusinessLogic\Basis\DataSet;

class TaskSet extends DataSet
{
    /**
     * @param $element
     * @return bool
     */
    public function push($element): bool
    {
        $result = false;

        $isValid = $element instanceof ITask;
        if ($isValid) {
            $this->collection[] = $element;
            $result = true;
        }

        return $result;
    }
}
