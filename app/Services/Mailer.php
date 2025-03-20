<?php
namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Mailer
{
    private $host;
    private $username;
    private $password;
    private $port;
    private $encryption;
    private $fromEmail;
    private $fromName;
    private $debug;
    
    public function __construct()
    {
        global $smtp;
        
        $this->host = $smtp['host'];
        $this->username = $smtp['user'];
        $this->password = $smtp['password'];
        $this->port = $smtp['port'];
        $this->encryption = $smtp['encryption'];
        $this->fromEmail = $smtp['from_email'];
        $this->fromName = $smtp['from_name'];
        $this->debug = defined('ENVIRONMENT') && ENVIRONMENT === 'development';
    }
    
    /**
     * Envoie un email
     * 
     * @param string|array $to Destinataire(s)
     * @param string $subject Sujet
     * @param string $body Corps du message
     * @param array $attachments Pièces jointes
     * @param array $additionalHeaders En-têtes supplémentaires
     * @return bool
     */
    public function send($to, $subject, $body, $attachments = [], $additionalHeaders = [])
    {
        // Si PHPMailer est disponible, l'utiliser en priorité
        if ($this->isPhpMailerAvailable()) {
            return $this->sendWithPhpMailer($to, $subject, $body, $attachments, $additionalHeaders);
        }
        
        // Sinon, utiliser les méthodes natives
        return $this->sendWithNativeMethods($to, $subject, $body, $attachments, $additionalHeaders);
    }
    
    /**
     * Vérifie si PHPMailer est disponible
     * @return bool
     */
    private function isPhpMailerAvailable() 
    {
        return class_exists('\PHPMailer\PHPMailer\PHPMailer');
    }
    
    /**
     * Envoie un email avec PHPMailer
     * @return bool
     */
    private function sendWithPhpMailer($to, $subject, $body, $attachments = [], $additionalHeaders = [])
    {
        try {
            // Créer une instance de PHPMailer
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host = $this->host;
            $mail->SMTPAuth = true;
            $mail->Username = $this->username;
            $mail->Password = $this->password;
            
            // Configuration du cryptage
            if ($this->encryption === 'tls') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            } elseif ($this->encryption === 'ssl') {
                $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
            }
            
            $mail->Port = $this->port;
            
            // Activer le debug en développement
            if ($this->debug) {
                $mail->SMTPDebug = 2; // Niveau de débogage: 0 = off, 1 = client, 2 = client/serveur
                $mail->Debugoutput = function($str, $level) {
                    error_log("PHPMailer Debug: $str");
                };
            }
            
            // Expéditeur
            $mail->setFrom($this->fromEmail, $this->fromName);
            
            // Destinataire(s)
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    $mail->addAddress($recipient);
                }
            } else {
                $mail->addAddress($to);
            }
            
            // En-têtes supplémentaires
            foreach ($additionalHeaders as $name => $value) {
                $mail->addCustomHeader($name, $value);
            }
            
            // Pièces jointes
            foreach ($attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $mail->addAttachment(
                        $attachment['path'], 
                        $attachment['name'] ?? basename($attachment['path']),
                        $attachment['encoding'] ?? 'base64',
                        $attachment['type'] ?? ''
                    );
                }
            }
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body); // Version texte pour les clients qui ne supportent pas l'HTML
            
            // Envoi
            return $mail->send();
            
        } catch (\Exception $e) {
            error_log("Erreur PHPMailer: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email avec les fonctions natives de PHP
     * @return bool
     */
    private function sendWithNativeMethods($to, $subject, $body, $attachments = [], $additionalHeaders = [])
    {
        // Valider les paramètres
        if (!$to || !$subject || !$body) {
            return false;
        }
        
        // Former les en-têtes
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
        ];
        
        // Ajouter les en-têtes supplémentaires
        foreach ($additionalHeaders as $key => $value) {
            $headers[] = "$key: $value";
        }
        
        // Configuration SMTP pour mail()
        $additionalParams = '-f ' . $this->fromEmail;
        
        if (is_array($to)) {
            $to = implode(', ', $to);
        }
        
        // Tentative d'envoi avec mail() de PHP
        if (mail($to, $subject, $body, implode("\r\n", $headers), $additionalParams)) {
            return true;
        }
        
        // Si mail() échoue, essayons d'utiliser la fonction SMTP native
        return $this->sendWithSmtp($to, $subject, $body, $headers);
    }
    
    /**
     * Envoie un email via SMTP
     * 
     * @param string $to Destinataire
     * @param string $subject Sujet
     * @param string $body Corps du message
     * @param array $headers En-têtes
     * @return bool
     */
    private function sendWithSmtp($to, $subject, $body, $headers = [])
    {
        try {
            // Connexion au serveur SMTP
            $socket = fsockopen(
                ($this->encryption == 'ssl' ? 'ssl://' : '') . $this->host,
                $this->port,
                $errNo,
                $errStr,
                30
            );
            
            if (!$socket) {
                throw new \Exception("Impossible de se connecter au serveur SMTP: $errStr ($errNo)");
            }
            
            // Lire la réponse du serveur
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '220') {
                throw new \Exception("Réponse SMTP inattendue: $response");
            }
            
            // Dire bonjour
            fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                throw new \Exception("Réponse EHLO inattendue: $response");
            }
            
            // Vider le buffer des réponses supplémentaires
            while (substr($response, 3, 1) == '-') {
                $response = fgets($socket, 515);
            }
            
            // TLS si nécessaire
            if ($this->encryption == 'tls') {
                fputs($socket, "STARTTLS\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '220') {
                    throw new \Exception("Réponse STARTTLS inattendue: $response");
                }
                
                // Démarrer TLS
                if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                    throw new \Exception("Échec de l'activation de la crypto TLS");
                }
                
                // Redire bonjour car la connexion a changé
                fputs($socket, "EHLO " . $_SERVER['HTTP_HOST'] . "\r\n");
                $response = fgets($socket, 515);
                if (substr($response, 0, 3) != '250') {
                    throw new \Exception("Réponse EHLO (TLS) inattendue: $response");
                }
                
                // Vider le buffer des réponses supplémentaires
                while (substr($response, 3, 1) == '-') {
                    $response = fgets($socket, 515);
                }
            }
            
            // Authentification
            fputs($socket, "AUTH LOGIN\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                throw new \Exception("Réponse AUTH inattendue: $response");
            }
            
            // Username
            fputs($socket, base64_encode($this->username) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '334') {
                throw new \Exception("Réponse USERNAME inattendue: $response");
            }
            
            // Password
            fputs($socket, base64_encode($this->password) . "\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '235') {
                throw new \Exception("Authentification échouée: $response");
            }
            
            // FROM
            fputs($socket, "MAIL FROM: <" . $this->fromEmail . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                throw new \Exception("Réponse MAIL FROM inattendue: $response");
            }
            
            // RCPT TO
            fputs($socket, "RCPT TO: <" . $to . ">\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250' && substr($response, 0, 3) != '251') {
                throw new \Exception("Réponse RCPT TO inattendue: $response");
            }
            
            // DATA
            fputs($socket, "DATA\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '354') {
                throw new \Exception("Réponse DATA inattendue: $response");
            }
            
            // Headers
            fputs($socket, "Subject: $subject\r\n");
            foreach ($headers as $header) {
                fputs($socket, "$header\r\n");
            }
            
            // Empty line between headers and body
            fputs($socket, "\r\n");
            
            // Body
            fputs($socket, "$body\r\n");
            
            // End of data
            fputs($socket, ".\r\n");
            $response = fgets($socket, 515);
            if (substr($response, 0, 3) != '250') {
                throw new \Exception("Réponse fin DATA inattendue: $response");
            }
            
            // QUIT
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            
            return true;
        } catch (\Exception $e) {
            // Log error
            error_log("Erreur SMTP: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoie un email de réinitialisation de mot de passe
     * 
     * @param string $email Adresse email
     * @param string $resetLink Lien de réinitialisation
     * @param string $name Nom de l'utilisateur
     * @return bool
     */
    public function sendPasswordReset($email, $resetLink, $name = '')
    {
        $subject = 'Réinitialisation de votre mot de passe';
        
        $body = "
            <html>
            <head>
                <title>Réinitialisation de votre mot de passe</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { width: 100%; max-width: 600px; margin: 0 auto; }
                    .header { background: #4e73df; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #4e73df; color: white; 
                              text-decoration: none; border-radius: 4px; margin-top: 20px; }
                    .footer { font-size: 12px; color: #777; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 10px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>Réinitialisation de mot de passe</h1>
                    </div>
                    <div class='content'>
                        <p>Bonjour " . ($name ?: '') . ",</p>
                        <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
                        <p>Pour réinitialiser votre mot de passe, cliquez sur le lien ci-dessous :</p>
                        <p><a href='$resetLink' class='button'>Réinitialiser mon mot de passe</a></p>
                        <p>Si le bouton ne fonctionne pas, veuillez copier et coller le lien suivant dans votre navigateur :</p>
                        <p>$resetLink</p>
                        <p>Ce lien expirera dans 24 heures.</p>
                        <p>Si vous n'avez pas demandé cette réinitialisation, vous pouvez ignorer cet email.</p>
                        <p>Merci,<br>L'équipe Sandrin DOSSOU</p>
                    </div>
                    <div class='footer'>
                        <p>Cet email a été envoyé automatiquement. Merci de ne pas y répondre.</p>
                        <p>&copy; " . date('Y') . " Sandrin DOSSOU - Tous droits réservés</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($email, $subject, $body);
    }
}
