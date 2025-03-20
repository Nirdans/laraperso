<?php
namespace App\Core;

class Router
{
    private static $routes = [];
    
    /**
     * Ajoute une route GET
     */
    public static function get($path, $callback)
    {
        self::$routes['GET'][$path] = $callback;
    }
    
    /**
     * Ajoute une route POST
     */
    public static function post($path, $callback)
    {
        self::$routes['POST'][$path] = $callback;
    }
    
    /**
     * Ajoute une route PUT
     */
    public static function put($path, $callback)
    {
        self::$routes['PUT'][$path] = $callback;
    }
    
    /**
     * Ajoute une route DELETE
     */
    public static function delete($path, $callback)
    {
        self::$routes['DELETE'][$path] = $callback;
    }
    
    /**
     * Analyse l'URL et exécute le callback correspondant
     */
    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Gérer les requêtes PUT et DELETE via POST avec _method
        if ($method == 'POST' && isset($_POST['_method'])) {
            if ($_POST['_method'] == 'PUT') {
                $method = 'PUT';
            } elseif ($_POST['_method'] == 'DELETE') {
                $method = 'DELETE';
            }
        }
        
        $uri = $_SERVER['REQUEST_URI'];
        $base = str_replace('/index.php', '', $_SERVER['SCRIPT_NAME']);
        $uri = str_replace($base, '', $uri);
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = trim($uri, '/');
        $uri = '/' . $uri;
        
        // Route par défaut
        if ($uri == '/') {
            $uri = '/home';
        }
        
        // Recherche de la route correspondante
        if (isset(self::$routes[$method][$uri])) {
            $callback = self::$routes[$method][$uri];
            if (is_callable($callback)) {
                return call_user_func($callback);
            } elseif (is_string($callback)) {
                // Format: Controller@method
                list($controller, $method) = explode('@', $callback);
                $controller = "App\\Controllers\\$controller";
                if (class_exists($controller)) {
                    $controller_obj = new $controller();
                    if (method_exists($controller_obj, $method)) {
                        return call_user_func([$controller_obj, $method]);
                    }
                }
            }
        }
        
        // Route non trouvée
        header("HTTP/1.0 404 Not Found");
        require BASE_PATH . '/views/errors/404.php';
    }
}
