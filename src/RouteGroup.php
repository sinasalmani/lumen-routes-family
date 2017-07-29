<?php
namespace Bamboo\Lumen\Routes;

use Bamboo\ArrayField\Extended\ArrayFieldsExtended;
use Laravel\Lumen\Application;

class RouteGroup
{
    public $as, $middleware, $prefix, $namespace, $domain, $families;
    public $routes = [], $groups = [];

    /**
     *
     *
     * @param string $nameAs
     * @param string $namespace
     * @param array $middleware
     * @param string $domain
     * @param string $prefix
     * @param RouteFamilies $families
     */
    public function __construct($nameAs = '', $namespace = '', array $middleware = [], $domain = '', $prefix = '')
    {
        $this->as = $nameAs
            ? $nameAs
            : null;
        $this->namespace = $namespace
            ? $namespace
            : null;
        $this->middleware = $middleware
            ? $middleware
            : [];
        $this->domain = $domain
            ? $domain
            : null;
        $this->prefix = $prefix
            ? $prefix
            : null;
        $this->families = new ArrayFieldsExtended();
    }

    /**
     * @param string $middlewareName
     */
    public function addMiddleware($middlewareName)
    {
        $this->middleware[] = $middlewareName;
    }

    /**
     * @param RouteFamily|string $family
     */
    public function family($family)
    {
        if ( is_a($family, RouteFamily::class) )
        {
            $rp = [
                'prefix' => $family->prefix,
                'suffix' => $family->suffix,
            ];
            $this->families->attach($rp, $family->name);

            return $family;
        }
        else
        {
            return $this->families->get($family);
        }
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return [
            'as'         => $this->as,
            'middleware' => $this->middleware,
            'namespace'  => $this->namespace,
            'prefix'     => $this->prefix,
            'domain'     => $this->domain,
        ];
    }

    /**
     * @param Route|RouteGroup $d
     */
    public function add($d)
    {
        if ( is_a($d, Route::class) )
        {
            $this->routes[] = $d;
        }
        elseif ( is_a($d, RouteGroup::class) )
        {
            $this->groups[] = $d;
        }
    }

    /**
     * @param \Laravel\Lumen\Application $app
     */
    public function declaration(Application $app)
    {
        $app->group($this->attributes(), function() use ($app)
        {
            foreach ( $this->routes as $route )
            {
                $family = $this->family($route->family);
                $route->declaration($app, $family);
            }
            foreach ( $this->groups as $group )
            {
                $group->declaration($app);
            }
        });
    }
}