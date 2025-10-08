<?php

namespace App;

class Router
{
    private array $routes;
    private Route $defaultRoute;

    private function _registerRoute(string $method, string $path, Route $route): void
    {
        $this->routes[$path][$method] = $route;
    }

    /**
     * Constructs and registers a route.
     *
     * @param Closure(): void $callback
     * @throws \ErrorException
     */
    private function _makeRoute(string $method, string $path, \Closure $callback): Route
    {
        if (isset($this->routes[$path][$method])) {
            throw new \ErrorException("'{$path}' has been already registered");
        }

        $route = new Route($callback);
        $this->_registerRoute($method, $path, $route);

        return $route;
    }

    /**
     * Register a GET route.
     *
     * @param Closure(): void $callback
     */
    public function GET(string $path, \Closure $callback): Route
    {
        return $this->_makeRoute('GET', $path, $callback);
    }

    /**
     * Register a POST route.
     *
     * @param Closure(): void $callback
     */
    public function POST(string $path, \Closure $callback): Route
    {
        return $this->_makeRoute('POST', $path, $callback);
    }
 
    /**
     * Register a default route, in case no other routes hit.
     *
     * @param Closure(): void $callback
     */
    public function DEFAULT(\Closure $callback): Route
    {
        if (isset($this->defaultRoute)) {
            throw new \ErrorException("A default route has been already registered");
        }

        $route = new Route($callback);
        $this->defaultRoute = $route;

        return $route;
    }

    /**
     * Handle all requests
     */
    public function handle(): void
    {
        $path = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$path][$method])) {
            $this->routes[$path][$method]->fire();
            return;
        }

        if (isset($this->defaultRoute)) {
            $this->defaultRoute->fire();
            return;
        }

        http_response_code(404);
        echo <<<'HTML'
        <!DOCTYPE html>
        <html lang="pl">
        <head>
            <meta charset="UTF8">
            <meta key="viewport" content="width=device-width, initial-scale=1.0">
            <title>Not found</title>
        </head>
        <body>
            <h1>Not found</h1>
            <p>This message was displayed because no default callback was set</p>
        </body>
        </html>
        HTML;
        die;
    }
}

class Route
{
    private array $middlewares;
    private \Closure $callback;

    /**
     * @param Closure(): void $callback
     */
    public function __construct(\Closure $callback)
    {
        $this->middlewares = [];
        $this->callback = $callback;
    }

    /**
     * @param Closure(): void $callback
     */
    public function with(\Closure $callback): Route
    {
        $this->middlewares[] = $callback;
        return $this;
    }

    public function fire(): void
    {
        foreach ($this->middlewares as $middleware) {
            ($middleware)();
        }
        ($this->callback)();
    }
}

