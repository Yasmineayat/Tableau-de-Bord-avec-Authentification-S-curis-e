<?php
require_once 'MailSender.php'; // Inclut le fichier 'MailSender.php' pour pouvoir utiliser la classe MailSender qui gère l'envoi d'emails via PHPMailer

$email = "ayatyasmine.1@gmail.com"; // Déclare l'adresse email du destinataire. C'est ici que l'email sera envoyé.

    // Crée le contenu de l'email
    $subject = "Your Verification Code"; // Sujet de l'email, dans ce cas, il s'agit d'un code de vérification
    $body = "<p>Hello ,</p>"; // Message d'introduction de l'email
    $body .= "<p>Your verification code is: <strong></strong></p>"; // Corps du message avec un code de vérification vide pour l'instant
    $body .= "<p>If you did not request this, please ignore this email.</p>"; // Message de sécurité, demandant au destinataire d'ignorer l'email si ce n'était pas une demande de leur part

    // Envoie l'email en utilisant PHPMailer via la classe MailSender
    $mailer = new MailSender(); // Crée une nouvelle instance de la classe MailSender pour envoyer l'email
    if (!$mailer->sendMail($email, $subject, $body)) { // Appelle la méthode sendMail() de la classe MailSender pour envoyer l'email
        echo "Failed to send verification email. Please contact support."; // Si l'envoi échoue, un message d'erreur est affiché
        exit(); // Le script s'arrête si l'email n'a pas pu être envoyé
    }

?>
