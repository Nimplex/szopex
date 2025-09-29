<?php

namespace App;

class Router
{
    private array $routes;

    /**
     * Register a route.
     *
     * @param Closure(array $query, array $body): mixed $callback
     *        Receives $_GET and $_POST arrays.
     */
    private function _registerRoute(string $method, string $path, \Closure $callback): Router
    {
        $this->routes[[$method, $path]] = $callback;
        return $this;
    }

    public function GET(string $path, \Closure $callback): Router
    {
        if (isset($this->routes[['GET', $path]])) {
            throw new \ErrorException("");
        }
        return $this->_registerRoute('GET', $path, $callback);
    }
}
