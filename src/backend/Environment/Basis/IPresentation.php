<?php

namespace Environment\Basis;


use Slim\Http\Response;

interface IPresentation
{
    public function getResponse():Response;

    public function process(): Response;
}
