<?php

namespace DataStorage\Basis;


use BusinessLogic\Basis\Content;
use BusinessLogic\Basis\DataSet;

class DataAccess implements IDataAccess
{
    private $access = null;
    private $status = false;
    private $rowCount = 0;
    private $data = null;

    function __construct(\PDO $access)
    {
        $this->setAccess($access)
            ->setSuccessStatus();
    }

    private function setAccess(\PDO $access): self
    {
        $this->access = $access;
        return $this;
    }

    protected function setRowCount(int $rowCount): self
    {
        $this->rowCount = $rowCount;
        return $this;
    }

    public function getRowCount(): int
    {
        return $this->rowCount;
    }

    protected function getAccess(): \PDO
    {
        return $this->access;
    }

    protected function prepareRequest(string $requestText)
    {
        $dbConnection = $this->getAccess();
        $request = $dbConnection->prepare($requestText);
        return $request;
    }

    protected function processWrite(\PDOStatement $request): self
    {
        $this->execute($request);

        $rowCount = $request->rowCount();
        $this->setRowCount($rowCount);

        $this->setData(new DataSet());

        return $this;
    }

    protected function setSuccessStatus(): self
    {
        $this->status = true;

        return $this;
    }

    protected function setFailStatus(): self
    {
        $this->status = false;

        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->status == true;

    }

    public function getData(): Content
    {
        return $this->data;
    }

    protected function setData(Content $data): self
    {
        $this->data = $data;
        return $this;
    }

    protected function processSuccess(): Content
    {
        $isSuccess = $this->isSuccess();
        $data = $this->getData();

        if ($isSuccess) {
            $data->setSuccessStatus();
        }
        if (!$isSuccess) {
            $data->setFailStatus();
        }

        return $data;
    }

    protected function execute(\PDOStatement $request): self
    {
        $isSuccess = $request->execute();

        if ($isSuccess) {
            $this->setSuccessStatus();
        }
        if (!$isSuccess) {
            $this->setFailStatus();
        }

        return $this;
    }
}
