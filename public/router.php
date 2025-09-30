<?php

class Route
{
    private array $middlewares;
    private \Closure $callback;

    public function __construct(\Closure $callback)
    {
        $this->middlewares = [];
        $this->callback = $callback;
    }

    public function with(\Closure $callback): void
    {
        $this->middlewares[] = $callback;
    }

    public function fire(array $query, array $body): void
    {
        foreach ($this->middlewares as $middleware) {
            ($middleware)($query, $body);
        }
        ($this->callback)($query, $body);
    }
}

class Router
{
    private array $routes;

    /**
     * Register a route.
     *
     * @param Closure(array $query, array $body): mixed $callback
     *        Receives $_GET and $_POST arrays.
     */
    private function _registerRoute(string $method, string $path, Route $route)
    {
        $this->routes[$path][$method] = $route;
    }

    /**
     * Register a GET route.
     * It is shorthand for `_registerRoute('GET', $path, $route);`
     *
     * @param Closure(array $query, array $body): mixed $callback
     *        Receives $_GET and $_POST arrays.
     *
     * @return Route
     */
    public function GET(string $path, \Closure $callback): Route
    {
        if (isset($this->routes[$path]['GET'])) {
            throw new \ErrorException("'{$path}' has been already registered");
        }

        $route = new Route($callback);

        $this->_registerRoute('GET', $path, $route);

        return $route;
    }

    /**
     * Register a POST route.
     * It is shorthand for `_registerRoute('POST', $path, $route);`
     *
     * @param Closure(array $query, array $body): mixed $callback
     *        Receives $_GET and $_POST arrays.
     *
     * @return Route
     */
    public function POST(string $path, \Closure $callback): Route
    {
        if (isset($this->routes[$path]['POST'])) {
            throw new \ErrorException("'{$path}' has been already registered");
        }

        $route = new Route($callback);

        $this->_registerRoute('POST', $path, $route);

        return $route;
    }
 
    /**
     * Register a ERROR route.
     * It is shorthand for `_registerRoute('ERROR', $path, $route);`
     *
     * @param Closure(array $query, array $body): mixed $callback
     *        Receives $_GET and $_POST arrays.
     *
     * @return Route
     */
    public function ERROR(string $code, \Closure $callback): Route
    {
        if (isset($this->routes[$code]['ERROR'])) {
            throw new \ErrorException("'{$code}' has been already registered");
        }

        $route = new Route($callback);

        $this->_registerRoute('ERROR', $code, $route);

        return $route;
    }

    /**
     * Handle all requests
     */
    public function handle()
    {
        $path = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$path][$method])) {
            $this->routes[$path][$method]->fire($_GET, $_POST);
        } elseif (empty($this->routes['404']['ERROR'])) {
            http_response_code(404);
            echo <<<HTML
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
        } else {
            $this->routes['404']['ERROR']->fire($_GET, $_POST);
        }
    }
}
