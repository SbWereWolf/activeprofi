<?php

namespace BusinessLogic\Task;


use BusinessLogic\Basis\DataSet;

class AmountSet extends DataSet
{
    /**
     * @param $element
     * @return bool
     */
    public function push($element): bool
    {
        $this->collection[] = $element;
        $result = true;

        return $result;
    }
}
