<?php

namespace App;

$ROLES = [
    'moderator' => 2,
    'administrator' => 3,
    'superadministrator' => 4
];

class Router
{
    private array $routes;
    private Route $defaultRoute;

    private function _registerRoute(string $method, string $path, Route $route): void
    {
        $this->routes[$method][$path] = $route;
    }

    /**
     * Constructs and registers a route.
     *
     * @param callable(): void $callback
     * @throws \ErrorException
     */
    private function _makeRoute(string $method, string $path, callable $callback, bool $check_auth = false, ?string $role = null): Route
    {
        if (isset($this->routes[$method][$path])) {
            throw new \ErrorException("'{$path}' has been already registered");
        }

        $route = new Route($callback, $check_auth, $role);
        $this->_registerRoute($method, $path, $route);

        return $route;
    }

    private function _match(string $path, string $pattern): ?array
    {
        $regex = preg_replace('#:([\w]+)#', '(?P<$1>[^/]+)', $pattern);
        $regex = "#^{$regex}$#";
        if (preg_match($regex, $path, $matches)) {
            return array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
        }
        return null;
    }

    /**
     * Register a GET route.
     *
     * @param callable(): void $callback
     */
    public function GET(string $path, callable $callback, ?bool $check_auth = false, ?string $role = null): Route
    {
        return $this->_makeRoute('GET', $path, $callback, $check_auth, $role);
    }

    /**
     * Register a POST route.
     *
     * @param callable(): void $callback
     */
    public function POST(string $path, callable $callback, ?bool $check_auth = false, ?string $role = null): Route
    {
        return $this->_makeRoute('POST', $path, $callback, $check_auth, $role);
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
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri);
        $path = $path === false ? '/' : $path['path'];
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
    public function __construct(callable $callback, bool $check_auth = false, ?string $role = null)
    {
        $this->role = $role;
        $this->check_auth = isset($role) || $check_auth;
        $this->callback = $callback(...);
    }

    public function fire(): void
    {
        @session_start();
    
        if ($this->check_auth && !isset($_SESSION['user_id'])) {
            require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/401.php';
            die;
        }

        if (
            isset($this->role) &&
            (
                !isset($_SESSION['user_role']) ||
                $ROLE[$_SESSION['user_role']] < $ROLE[$role]
            )
        ) {
            require $_SERVER['DOCUMENT_ROOT'] . '/../resources/errors/401.php';
            die;
        }

        ($this->callback)();
    }
}
