<?php

namespace App\Commands;

class Make
{
    protected $modelsPath = BASE_PATH . '/app/Models/';
    protected $controllersPath = BASE_PATH . '/app/Controllers/';

    public function model($name)
    {
        $name = ucfirst($name);
        $filepath = $this->modelsPath . $name . '.php';

        $stub = <<<PHP
<?php

namespace App\Models;

use App\Core\Model;

class {$name} extends Model
{
    /**
     * Nom de la table associée au modèle
     * @var string
     */
    protected \$table = ''; // À définir

    /**
     * Les attributs qui peuvent être assignés en masse
     * @var array
     */
    protected \$fillable = [];
}
PHP;

        $this->createFile($filepath, $stub);
        echo "Modèle créé : {$filepath}\n";
    }

    public function controller($name)
    {
        $name = ucfirst($name);
        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }
        
        $filepath = $this->controllersPath . $name . '.php';

        $stub = <<<PHP
<?php

namespace App\Controllers;

use App\Core\Controller;

class {$name} extends Controller
{
    /**
     * Affiche la liste des ressources
     * @return void
     */
    public function index()
    {
        // Code à implémenter
    }

    /**
     * Affiche le formulaire de création
     * @return void
     */
    public function create()
    {
        // Code à implémenter
    }

    /**
     * Enregistre une nouvelle ressource
     * @return void
     */
    public function store()
    {
        // Code à implémenter
    }

    /**
     * Affiche une ressource spécifique
     * @param int \$id
     * @return void
     */
    public function show(\$id)
    {
        // Code à implémenter
    }

    /**
     * Affiche le formulaire d'édition
     * @param int \$id
     * @return void
     */
    public function edit(\$id)
    {
        // Code à implémenter
    }

    /**
     * Met à jour une ressource
     * @param int \$id
     * @return void
     */
    public function update(\$id)
    {
        // Code à implémenter
    }

    /**
     * Supprime une ressource
     * @param int \$id
     * @return void
     */
    public function destroy(\$id)
    {
        // Code à implémenter
    }
}
PHP;

        $this->createFile($filepath, $stub);
        echo "Contrôleur créé : {$filepath}\n";
    }

    protected function createFile($filepath, $content)
    {
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        if (file_exists($filepath)) {
            echo "Erreur : Le fichier existe déjà.\n";
            exit(1);
        }

        file_put_contents($filepath, $content);
    }
}
