<?php
namespace Bamboo\Lumen\Routes;

use Bamboo\ArrayField\Extended\ArrayFieldsExtended;
use Bamboo\ArrayField\Extended\ArrayFieldsExtendedMapped;

class RouteFamily
{
    public $name, $prefix, $suffix;

    public function __construct($name, $prefix = '', $suffix = '')
    {
        $this->name = $name;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
    }
}