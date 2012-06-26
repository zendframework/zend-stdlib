<?php
namespace Zend\Stdlib;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Stdlib\RequestInterface as Request;

interface DispatchableInterface
{
    public function dispatch(Request $request, Response $response = null);
}
