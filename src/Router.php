<?php

namespace App;

class Router
{
    private array $routes;
    private Route $defaultRoute;

    private function _registerRoute(string $method, string $path, Route $route, bool $check_auth): void
    {
        $this->routes[$method][$path] = $route;
    }

    /**
     * Constructs and registers a route.
     *
     * @param callable(): void $callback
     * @throws \ErrorException
     */
    private function _makeRoute(string $method, string $path, callable $callback, bool $check_auth): Route
    {
        if (isset($this->routes[$method][$path])) {
            throw new \ErrorException("'{$path}' has been already registered");
        }

        $route = new Route($callback, $check_auth);
        $this->_registerRoute($method, $path, $route, $check_auth);

        return $route;
    }

    private function _match(string $path, string $pattern): ?array
    {
        $types = [
            'int' => '\d+',
            'string' => '[a-zA-Z0-9_-]+',
        ];

        $regex = preg_replace_callback(
            '#:(\w+)(?::(\w+))?#',
            function ($matches) use ($types) {
                $paramName = $matches[1];
                $paramType = $matches[2] ?? null;
                if ($paramType && isset($types[$paramType])) {
                    $pattern = $types[$paramType];
                } else {
                    $pattern = '[^/]+';
                }
                return "(?P<{$paramName}>{$pattern})";
            },
            $pattern
        );

        $regex = "#^{$regex}$#i";
        
        if (preg_match($regex, $path, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            foreach ($params as $key => $value) {
                if (preg_match('/\(\?P<' . preg_quote($key) . '>' . $types['int'] . '\)/', $regex)) {
                    $params[$key] = (int) $value;
                }
            }
            return $params;
        }
        return null;
    }

    /**
     * Register a GET route.
     *
     * @param callable(): void $callback
     */
    public function GET(string $path, callable $callback, bool $check_auth = false): Route
    {
        return $this->_makeRoute('GET', $path, $callback, $check_auth);
    }

    /**
     * Register a POST route.
     *
     * @param callable(): void $callback
     */
    public function POST(string $path, callable $callback, bool $check_auth = false): Route
    {
        return $this->_makeRoute('POST', $path, $callback, $check_auth);
    }
 
    /**
     * Register a default route, in case no other routes hit.
     *
     * @throws \ErrorException
     * @param callable(): void $callback
     */
    public function DEFAULT(callable $callback): Route
    {
        if (isset($this->defaultRoute)) {
            throw new \ErrorException("A default route has been already registered");
        }

        // never check auth on default route (it should be accessible to everyone)
        $route = new Route($callback, false);
        $this->defaultRoute = $route;

        return $route;
    }


    /**
     * Handle all requests
     */
    public function handle(): void
    {
        global $_ROUTE;
        $path = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if (isset($this->routes[$method])) {
            foreach ($this->routes[$method] as $pattern => $route) {
                $params = $this->_match($path, $pattern);
                if ($params !== null) {
                    $_ROUTE = $params;
                    $route->fire();
                    return;
                }
            }
        }

        if (isset($this->defaultRoute)) {
            $_ROUTE = [];
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
    private \Closure $callback;
    private bool $check_auth;

    /**
     * @param callable(): void $callback
     */
    public function __construct(callable $callback, bool $check_auth)
    {
        $this->check_auth = $check_auth;
        $this->callback = $callback(...);
    }

    public function fire(): void
    {
        @session_start();
        if ($this->check_auth && !isset($_SESSION['user_id'])) {
            require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/401.php';
            die;
        }

        ($this->callback)();
    }
}
