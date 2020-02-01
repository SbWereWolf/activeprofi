<?php

namespace BusinessLogic\Basis;


interface Content
{

    public function push($element): bool;

    public function next();

    public function setSuccessStatus();

    public function setFailStatus();

    public function isSuccess(): bool;
}
