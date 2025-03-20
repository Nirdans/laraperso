<?php

/**
 * Retourne l'URL d'un asset
 * @param string $path Chemin relatif de l'asset
 * @return string
 */
function asset($path) {
    global $current;
    $basePath = rtrim($current['domain'], '/');
    $assetPath = '/public/assets/' . ltrim($path, '/');
    return $basePath . $assetPath;
}

/**
 * Retourne l'URL d'un fichier uploadé
 * @param string $path Chemin relatif du fichier
 * @return string
 */
function upload_url($path) {
    global $current;
    $basePath = rtrim($current['domain'], '/');
    $uploadPath = '/public/uploads/' . ltrim($path, '/');
    return $basePath . $uploadPath;
}

/**
 * Inclut une vue partielle
 * @param string $partial Nom de la vue partielle
 * @param array $data Variables à passer à la vue
 * @return string
 */
function view($partial, $data = []) {
    return View::include($partial, $data);
}

/**
 * Retourne le chemin vers un template
 * @param string $template Nom du template
 * @return string
 */
function theme_path($template) {
    global $assets;
    $theme = $assets['theme'] ?? 'default';
    return BASE_PATH . '/public/themes/' . $theme . '/' . ltrim($template, '/');
}

/**
 * Inclut un template
 * @param string $template Nom du template
 * @param array $data Variables à passer au template
 * @return void
 */
function theme_include($template, $data = []) {
    extract($data);
    include theme_path($template);
}
