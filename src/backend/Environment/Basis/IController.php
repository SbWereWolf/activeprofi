<?php

namespace Environment\Basis;

use Slim\Http\Response;


interface IController
{
    public function process(): Response;
}
