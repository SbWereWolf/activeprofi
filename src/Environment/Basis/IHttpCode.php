<?php

namespace Environment\Basis;

use Slim\Http\Response;


interface IHttpCode
{
    public function process(bool $isSuccess): Response;
}
