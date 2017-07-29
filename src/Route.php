<?php
namespace Bamboo\Lumen\Routes;

use Laravel\Lumen\Application;

class Route
{
    const METHODS = [
        'GET'     => 1,
        'POST'    => 2,
        'PUT'     => 4,
        'PATCH'   => 8,
        'DELETE'  => 16,
        'OPTIONS' => 32,
    ];
    public $family, $as, $method, $uri, $uses;

    /**
     * Route constructor.
     *
     * @param string $family
     * @param string $nameAs
     * @param string|int $routeMethod
     * @param string $uri
     * @param \Bamboo\Lumen\Routes\RouteUsesMethod $uses
     */
    public function __construct($family, $nameAs, $routeMethod, $uri, RouteUsesMethod $uses)
    {
        $this->familyName = $family;
        $this->as = $nameAs;
        //TODO: method must be validate
        $this->method = is_string($routeMethod)
            ? self::methodNameToInt($routeMethod)
            : (int) $routeMethod;
        $this->uri = $uri;
        $this->uses = $uses;
    }

    private static function methodNameToInt($methods)
    {
        $methods = explode('|', $methods);
        $i = 0;
        foreach ( $methods as $method )
        {
            $method = strtoupper(trim($method));
            $check = false;
            foreach ( self::METHODS as $mt => $vl )
            {
                if ( $method == $mt )
                {
                    $check = true;
                    break;
                }
            }
            if ( $check !== false )
            {
                $i = $i | self::METHODS[ $method ];
            }
        }

        return $i;
    }

    /**
     * @return array
     */
    public function methods()
    {
        $l = count(self::METHODS);
        $bin = decbin($this->method);
        $bin = strrev(substr($bin, strlen($bin) - $l));
        $k = [];
        $loop = min($l, strlen($bin));
        for ( $i = 0; $i < $loop; $i++ )
        {
            if ( $bin{$i} === "1" )
            {
                $k[] = pow(2, $i);
            }
        }

        return $k;
    }

    /**
     * @return array
     */
    public function methodsNames()
    {
        $m = $this->methods();

        $methods = array_flip(self::METHODS);
        $names = [];
        foreach ( $m as $mt )
        {
            if ( isset( $methods[ $mt ] ) )
            {
                $names[] = strtolower($methods[ $mt ]);
            }
        }

        return $names;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'as'   => $this->as,
            'uses' => $this->uses . '',
        ];
    }

    /**
     * @param \Laravel\Lumen\Application $app
     * @param RouteFamily $family
     */
    public function declaration(Application $app, RouteFamily $family)
    {
        $mt = $this->methodsNames();
        foreach ( $mt as $method )
        {
            $app->$method($family->prefix . $this->uri . $family->suffix, $this->attributes());
        }
    }
}