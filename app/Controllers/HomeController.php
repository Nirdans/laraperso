<?php
namespace App\Controllers;

class HomeController
{
    public function __construct()
    {
        // Vérifier si l'utilisateur est connecté pour certaines pages
        // Dans ce cas, la page d'accueil peut être accessible sans connexion
    }
    
    /**
     * Affiche la page d'accueil
     */
    public function index()
    {
        require BASE_PATH . '/views/home.php';
    }
}
