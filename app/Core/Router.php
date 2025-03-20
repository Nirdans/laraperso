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
     * Vérifie si une route correspond à l'URI actuelle et extrait les paramètres
     * 
     * @param string $route La définition de la route (peut contenir des paramètres {param})
     * @param string $uri L'URI actuelle
     * @return array|false Tableau de paramètres ou false si pas de correspondance
     */
    private function matchRoute($route, $uri)
    {
        // Si la route ne contient pas de paramètres, faire une correspondance directe
        if (strpos($route, '{') === false) {
            return $route === $uri ? [] : false;
        }
        
        // Convertir la définition de route en expression régulière
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $route);
        $pattern = '#^' . $pattern . '$#';
        
        // Extraire les noms des paramètres
        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route, $paramNames);
        $paramNames = $paramNames[1]; // Obtenir les noms sans les accolades
        
        // Vérifier si l'URI correspond au pattern et extraire les valeurs
        if (preg_match($pattern, $uri, $paramValues)) {
            array_shift($paramValues); // Supprimer la première valeur (correspondance complète)
            
            // Associer les noms et les valeurs des paramètres
            $params = [];
            foreach ($paramNames as $index => $name) {
                $params[$name] = isset($paramValues[$index]) ? $paramValues[$index] : null;
            }
            
            return $params;
        }
        
        return false;
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
        if (isset(self::$routes[$method])) {
            foreach (self::$routes[$method] as $route => $callback) {
                $params = $this->matchRoute($route, $uri);
                
                if ($params !== false) {
                    if (is_callable($callback)) {
                        return call_user_func_array($callback, array_values($params));
                    } elseif (is_string($callback)) {
                        // Format: Controller@method
                        list($controller, $method) = explode('@', $callback);
                        $controller = "App\\Controllers\\$controller";
                        if (class_exists($controller)) {
                            $controller_obj = new $controller();
                            if (method_exists($controller_obj, $method)) {
                                return call_user_func_array([$controller_obj, $method], array_values($params));
                            }
                        }
                    }
                    break;
                }
            }
        }
        
        // Route non trouvée
        header("HTTP/1.0 404 Not Found");
        require BASE_PATH . '/views/errors/404.php';
    }
}
