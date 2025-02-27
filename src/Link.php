<?php

namespace GDebrauwer\Hateoas;

use Illuminate\Routing\Router;

class Link
{
    /**
     * The name of the link.
     *
     * @var string
     */
    protected $name;

    /**
     * The route name of the link.
     *
     * @var string
     */
    protected $routeName;

    /**
     * The route parameters of the link.
     *
     * @var array
     */
    protected $routeParameters;

    /**
     * Create a new HATEOAS link.
     *
     * @param string $routeName
     * @param array $routeParameters
     *
     * @return void
     */
    public function __construct(string $routeName, array $routeParameters = [])
    {
        $this->name = $routeName;
        $this->routeName = $routeName;
        $this->routeParameters = $routeParameters;
    }

    /**
     * Create a new HATEOAS link.
     *
     * @param string $routeName
     * @param array $routeParameters
     *
     * @return self
     */
    public static function make(string $routeName, array $routeParameters = [])
    {
        return new self($routeName, $routeParameters);
    }

    /**
     * Set name of link.
     *
     * @param string $name
     *
     * @return self
     */
    public function as(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name of the link.
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get the HTTP method of the link.
     *
     * @return string
     */
    public function method()
    {
        return once(function () {
            return collect($this->route()->methods)->first(function ($method) {
                return $method !== 'HEAD';
            });
        });
    }

    /**
     * Get the URL path of the link.
     *
     * @return string
     */
    public function path()
    {
        return once(function () {
            return route($this->routeName, $this->routeParameters, false);
        });
    }

    /**
     * Get the URL of the link.
     *
     * @return string
     */
    public function url()
    {
        return once(function () {
            return route($this->routeName, $this->routeParameters);
        });
    }

    /**
     * Get the route name of the link.
     *
     * @return string
     */
    public function routeName()
    {
        return $this->routeName;
    }

    /**
     * Get the route of the link.
     *
     * @return \Illuminate\Routing\Route
     */
    protected function route()
    {
        return once(function () {
            return app(Router::class)->getRoutes()->getByName($this->routeName);
        });
    }
}
