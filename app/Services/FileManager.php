<?php
namespace App\Services;

class FileManager
{
    /**
     * Répertoire par défaut pour les uploads
     * @var string
     */
    protected $uploadDir;
    
    /**
     * Types MIME autorisés
     * @var array
     */
    protected $allowedMimeTypes = [
        'image/jpeg', 
        'image/png', 
        'image/gif', 
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain'
    ];
    
    /**
     * Taille maximale des fichiers en octets (10 Mo par défaut)
     * @var int
     */
    protected $maxFileSize = 10485760; // 10 Mo
    
    /**
     * Constructeur
     * @param string $uploadDir Répertoire d'upload personnalisé
     */
    public function __construct($uploadDir = null)
    {
        $this->uploadDir = $uploadDir ?: BASE_PATH . '/assets/uploads/';
        
        // Créer le répertoire s'il n'existe pas
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }
    
    /**
     * Définir les types MIME autorisés
     * @param array $mimeTypes Liste des types MIME
     * @return $this
     */
    public function setAllowedMimeTypes(array $mimeTypes)
    {
        $this->allowedMimeTypes = $mimeTypes;
        return $this;
    }
    
    /**
     * Définir la taille maximale des fichiers
     * @param int $size Taille en octets
     * @return $this
     */
    public function setMaxFileSize(int $size)
    {
        $this->maxFileSize = $size;
        return $this;
    }
    
    /**
     * Télécharger un fichier
     * @param array $file Tableau $_FILES
     * @param string $customName Nom personnalisé (facultatif)
     * @return array|false Informations sur le fichier ou false si échec
     */
    public function upload($file, $customName = null)
    {
        // Vérifier si le fichier existe
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            $this->setError('Aucun fichier téléchargé');
            return false;
        }
        
        // Vérifier s'il y a une erreur dans le fichier
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->setError($this->getUploadErrorMessage($file['error']));
            return false;
        }
        
        // Vérifier le type MIME
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        
        if (!in_array($mime, $this->allowedMimeTypes)) {
            $this->setError('Type de fichier non autorisé');
            return false;
        }
        
        // Vérifier la taille du fichier
        if ($file['size'] > $this->maxFileSize) {
            $this->setError('Le fichier est trop grand');
            return false;
        }
        
        // Créer un nom unique pour le fichier
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $customName ? $customName . '.' . $extension : 
            uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        
        $destination = $this->uploadDir . $fileName;
        
        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $this->setError('Erreur lors du déplacement du fichier');
            return false;
        }
        
        // Retourner les informations sur le fichier
        return [
            'name' => $fileName,
            'original_name' => $file['name'],
            'mime_type' => $mime,
            'size' => $file['size'],
            'path' => $destination,
            'url' => $this->getFileUrl($fileName)
        ];
    }
    
    /**
     * Télécharge plusieurs fichiers
     * @param array $files Tableau $_FILES avec plusieurs fichiers
     * @return array Informations sur les fichiers téléchargés
     */
    public function uploadMultiple($files)
    {
        $uploaded = [];
        $errors = [];
        
        // Réorganiser le tableau $_FILES si nécessaire
        if (isset($files['name']) && is_array($files['name'])) {
            $fileCount = count($files['name']);
            $fileKeys = array_keys($files);
            
            for ($i = 0; $i < $fileCount; $i++) {
                $file = [];
                foreach ($fileKeys as $key) {
                    $file[$key] = $files[$key][$i];
                }
                
                $result = $this->upload($file);
                if ($result) {
                    $uploaded[] = $result;
                } else {
                    $errors[] = "Erreur lors du téléchargement du fichier {$i}: " . $this->getError();
                }
            }
        } else {
            // Cas où chaque fichier est un élément du tableau
            foreach ($files as $file) {
                $result = $this->upload($file);
                if ($result) {
                    $uploaded[] = $result;
                } else {
                    $errors[] = "Erreur lors du téléchargement: " . $this->getError();
                }
            }
        }
        
        return [
            'uploaded' => $uploaded,
            'errors' => $errors
        ];
    }
    
    /**
     * Supprimer un fichier
     * @param string $fileName Nom du fichier à supprimer
     * @return bool
     */
    public function delete($fileName)
    {
        $filePath = $this->uploadDir . $fileName;
        
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        
        return false;
    }
    
    /**
     * Obtenir l'URL d'accès à un fichier
     * @param string $fileName Nom du fichier
     * @return string
     */
    public function getFileUrl($fileName)
    {
        global $current;
        $basePath = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->uploadDir);
        return $current['domain'] . '/' . 'assets/uploads/' . $fileName;
    }
    
    /**
     * Définir un message d'erreur
     * @param string $message Message d'erreur
     */
    protected function setError($message)
    {
        $_SESSION['file_error'] = $message;
    }
    
    /**
     * Récupérer le dernier message d'erreur
     * @return string|null
     */
    public function getError()
    {
        $error = $_SESSION['file_error'] ?? null;
        unset($_SESSION['file_error']);
        return $error;
    }
    
    /**
     * Obtenir un message d'erreur pour les codes d'erreur d'upload
     * @param int $errorCode Code d'erreur
     * @return string
     */
    protected function getUploadErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return "Le fichier téléchargé dépasse la directive upload_max_filesize dans php.ini";
            case UPLOAD_ERR_FORM_SIZE:
                return "Le fichier téléchargé dépasse la directive MAX_FILE_SIZE spécifiée dans le formulaire HTML";
            case UPLOAD_ERR_PARTIAL:
                return "Le fichier n'a été que partiellement téléchargé";
            case UPLOAD_ERR_NO_FILE:
                return "Aucun fichier n'a été téléchargé";
            case UPLOAD_ERR_NO_TMP_DIR:
                return "Dossier temporaire manquant";
            case UPLOAD_ERR_CANT_WRITE:
                return "Échec de l'écriture du fichier sur le disque";
            case UPLOAD_ERR_EXTENSION:
                return "Une extension PHP a arrêté le téléchargement de fichier";
            default:
                return "Erreur inconnue lors du téléchargement";
        }
    }
}
