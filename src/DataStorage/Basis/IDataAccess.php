<?php

namespace DataStorage\Basis;


use BusinessLogic\Basis\Content;

interface IDataAccess
{
    public function getRowCount(): int;

    public function isSuccess(): bool;

    public function getData(): Content;
}
