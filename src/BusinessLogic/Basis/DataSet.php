<?php

namespace BusinessLogic\Basis;


class DataSet implements Content
{

    protected $collection = array();
    private $status = false;

    /**
     * @param $element
     * @return bool
     * @throws \Exception
     */
    public function push($element): bool
    {
        throw new \Exception('Method push($element) Not Implemented');
    }

    public function next()
    {
        foreach ($this->collection as $element) {
            yield $element;
        }
        return;
    }

    public function setSuccessStatus()
    {
        $this->status = true;
    }

    public function setFailStatus()
    {
        $this->status = false;
    }

    public function isSuccess(): bool
    {
        return $this->status == true;

    }
}
