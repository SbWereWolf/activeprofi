<?php

namespace DataStorage\Basis;


class DataSource
{
    private $dsn = '';
    private $username = '';
    private $passwd = '';
    private $options = array();

    public function __construct(string $dsn)
    {
        $this->setDsn($dsn);
    }

    public function getDsn(): string
    {
        return $this->dsn;
    }

    public function setDsn(string $dsn): DataSource
    {
        $this->dsn = $dsn;
        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): DataSource
    {
        $this->username = $username;
        return $this;
    }

    public function getPasswd(): string
    {
        return $this->passwd;
    }

    public function setPasswd(string $passwd): DataSource
    {
        $this->passwd = $passwd;
        return $this;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function setOptions(array $options): DataSource
    {
        $this->options = $options;
        return $this;
    }
}
