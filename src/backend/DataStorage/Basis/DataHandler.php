<?php

namespace DataStorage\Basis;


class DataHandler
{
    protected $access = null;
    private $dataSource = null;

    public function __construct(DataSource $dataPath)
    {
        $this->setDataSource($dataPath);
    }

    private function setDataSource(DataSource $dataSource): self
    {
        $this->dataSource = $dataSource;
        return $this;
    }

    private function getDataSource(): DataSource
    {
        return $this->dataSource;
    }

    protected function begin(): bool
    {
        $result = $this->getAccess()->beginTransaction();

        return $result;
    }

    protected function getAccess(): \PDO
    {
        $access = $this->access;
        $isExists = !empty($access);

        if (!$isExists) {
            $dataSource = $this->getDataSource();
            $access = new \PDO(
                $dataSource->getDsn(),
                $dataSource->getUsername(),
                $dataSource->getPasswd(),
                $dataSource->getOptions());
            $this->access = $access;
        }

        return $access;
    }

    protected function commit(): bool
    {
        $result = $this->getAccess()->commit();

        $this->dropAccess();

        return $result;
    }

    protected function dropAccess(): self
    {
        $this->access = null;

        return $this;
    }

    protected function rollBack(): bool
    {
        $result = $this->getAccess()->rollBack();

        $this->dropAccess();

        return $result;
    }
}
