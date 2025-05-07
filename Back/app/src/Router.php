<?php

namespace App;

use App\Utils\Route;
use App\Utils\JWT;

class Router {
    protected array $routes = [];
    protected string $url;
    protected string $method;

    public function __construct() {
        $this->url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->method = $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Register all controllers and their routes.
     *
     * @param array $controllers List of controller classes to register
     */
    public function registerControllers(array $controllers) {
        foreach ($controllers as $controller) {
            $reflection = new \ReflectionClass($controller);
            foreach ($reflection->getMethods() as $method) {
                $attributes = $method->getAttributes(Route::class);
                foreach ($attributes as $attribute) {
                    $instance = $attribute->newInstance();
                    $this->register(
                        $instance->method, 
                        $instance->path, 
                        $controller, 
                        $method->getName(), 
                        $instance->middlewares
                    );
                }
            }
        }
    }

    /**
     * Register a single route.
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $route URL pattern for the route
     * @param string $controller Controller class handling the route
     * @param string $controllerMethod Method name in the controller
     * @param bool $authRequired Whether authentication is required for this route
     */
    public function register(string $method, string $route, 
    string $controller, string $controllerMethod, array $middlewares) {
        $this->routes[$method][$route] = [$controller, $controllerMethod, $middlewares];
    }

    /**
     * Execute the route matching the current request.
     */
    public function run() {
        $allowedOrigins = [
            'http://localhost:5173',
            'http://127.0.0.1:5173'
        ];
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header("Access-Control-Allow-Credentials: true");
            header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: Content-Type, Authorization");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit();
        }

        $response = null;
        $request = $_REQUEST; 
        ob_start();

        foreach ($this->routes[$this->method] as $route => $action) {
            if ($this->matchRule($this->url, $route)) {
                list($controller, $method, $middlewares) = $action;
                $pathParams = $this->extractParams($this->url, $route);
                foreach ($middlewares as $middlewareDefinition) {
                    if (is_array($middlewareDefinition)) {
                        $middlewareClass = $middlewareDefinition[0];
                        $middlewareArgs = array_slice($middlewareDefinition, 1);
                        $middleware = new $middlewareClass(...$middlewareArgs);
                    } else {
                        $middlewareClass = $middlewareDefinition;
                        $middleware = new $middlewareClass();
                    }

                    if (method_exists($middleware, 'handle') && !$middleware->handle($request)) {
                        exit;
                    }
                }

                $queryParams = $_GET;
                $params = array_merge($pathParams, $queryParams);
                $controllerInstance = new $controller($params);

                if (method_exists($controllerInstance, $method)) {
                    try {
                        $response = call_user_func_array([ $controllerInstance, $method ], array_values($params));
                    } catch (\Exception $e) {
                        http_response_code(500);
                        $response = ["error" => $e->getMessage()];
                    }
                } else {
                    http_response_code(405);
                    $response = ["error" => "Method Not Allowed"];
                }
                break;
            }
        }
        if ($response === null) {
            http_response_code(404);
            $response = ["error" => "Not Found"];
        }

        ob_end_clean(); 
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    /**
     * Check the authorization of the current request.
     *
     * @return bool True if the request is authorized, false otherwise
     */
    protected function checkAuth() {
        $headers = getallheaders();
        if (!isset($headers['Authorization'])) {
            return false;
        }

        $authHeader = $headers['Authorization'];
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $jwt = $matches[1];
            return JWT::verify($jwt);
        }

        return false;
    }

    /**
     * Match the URL against the given route pattern.
     *
     * @param string $url The requested URL
     * @param string $route The route pattern to match against
     * @return bool True if the URL matches the route pattern, false otherwise
     */
    protected function matchRule($url, $route) {
        $urlParts = explode('/', trim($url, '/'));
        $routeParts = explode('/', trim($route, '/'));
        if (count($urlParts) !== count($routeParts)) {
            return false;
        }
        foreach ($routeParts as $index => $routePart) {
            if ($routePart !== $urlParts[$index] && strpos($routePart, ':') !== 0) {
                return false;
            }
        }
        return true;
    }

    /**
     * Extract parameters from the URL based on the route pattern.
     *
     * @param string $url The requested URL
     * @param string $route The route pattern with parameter placeholders
     * @return array Associative array of parameters extracted from the URL
     */
    protected function extractParams($url, $route) {
        $params = [];
        $urlParts = explode('/', trim($url, '/'));
        $routeParts = explode('/', trim($route, '/'));
        foreach ($routeParts as $index => $routePart) {
            if (strpos($routePart, ':') === 0 && isset($urlParts[$index])) {
                $paramName = substr($routePart, 1);
                $params[$paramName] = $urlParts[$index];
            }
        }
        return $params;
    }
}
