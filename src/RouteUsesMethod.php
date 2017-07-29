<?php
namespace Bamboo\Lumen\Routes;

class RouteUsesMethod
{
    public $class, $method;

    public function __construct($class, $method)
    {
        $this->class = $class;
        $this->method = $method;
    }
    public function __toString()
    {
        return $this->class . '@' . $this->method;
    }
}