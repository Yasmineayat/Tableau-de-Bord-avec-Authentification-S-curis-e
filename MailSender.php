<?php 
use PHPMailer\PHPMailer\PHPMailer;  // Utilisation de la classe PHPMailer pour envoyer des emails
use PHPMailer\PHPMailer\Exception; // Utilisation de la classe Exception pour la gestion des erreurs de PHPMailer

require 'vendor/autoload.php'; // Charge automatiquement les dépendances nécessaires pour PHPMailer via Composer

// Déclaration de la classe MailSender qui sera utilisée pour envoyer des emails
class MailSender {
    private $mail; // Déclare une propriété pour l'objet PHPMailer

    // Constructeur de la classe MailSender qui initialise la configuration SMTP
    public function __construct() {
        $this->mail = new PHPMailer(true); // Crée une instance PHPMailer avec gestion des exceptions activée

        try {
            // Configuration du serveur SMTP pour envoyer des emails
            $this->mail->isSMTP(); // Définit PHPMailer pour utiliser le protocole SMTP pour l'envoi d'emails
            $this->mail->Host = 'smtp.gmail.com'; // Indique l'adresse du serveur SMTP (ici, Gmail)
            $this->mail->SMTPAuth = true; // Active l'authentification SMTP
            $this->mail->Username = 'pfaproject77@gmail.com'; // Spécifie l'email utilisé pour envoyer les emails
            $this->mail->Password = 'lidgfkvcvegqoaco'; // Mot de passe ou mot de passe spécifique à l'application (attention à la sécurité)
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Définit le type de cryptage (SSL/TLS)
            $this->mail->Port = 465; // Définit le port SMTP (465 pour SSL, 587 pour TLS)

            // Détails de l'expéditeur de l'email
            $this->mail->setFrom('test@gmail.com', 'Projet PHP'); // Définit l'email et le nom de l'expéditeur
        } catch (Exception $e) {
            // Si une erreur se produit lors de la configuration du serveur SMTP, afficher un message d'erreur
            echo "Mailer Error: {$this->mail->ErrorInfo}";
        }
    }

    // Méthode publique pour envoyer un email
    public function sendMail($to, $subject, $body) {
        try {
            // Ajouter le destinataire de l'email
            $this->mail->addAddress($to); // Spécifie l'adresse email du destinataire

            // Configuration du contenu de l'email
            $this->mail->isHTML(true); // Définit que l'email sera envoyé en format HTML
            $this->mail->Subject = $subject; // Définit le sujet de l'email
            $this->mail->Body = $body; // Définit le contenu de l'email (le corps)

            // Envoi de l'email
            $this->mail->send(); // Envoie l'email
            return true; // Retourne true si l'email a été envoyé avec succès
        } catch (Exception $e) {
            // Si une erreur se produit lors de l'envoi de l'email, afficher un message d'erreur
            echo "Email could not be sent. Mailer Error: {$this->mail->ErrorInfo}";
            return false; // Retourne false si l'email n'a pas pu être envoyé
        }
    }
}
?>
