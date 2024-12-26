<?php
session_start(); // Démarre la session PHP pour accéder et gérer les données de session

require_once 'MailSender.php'; // Inclut le fichier 'MailSender.php' pour utiliser la classe MailSender afin d'envoyer des emails

// Vérifie si l'utilisateur est connecté en vérifiant si une session 'user' existe
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Si l'utilisateur n'est pas connecté, il est redirigé vers la page de login
    exit(); // Terminer l'exécution du script pour empêcher l'accès au reste du code
}

// Vérifie si le code de vérification n'a pas encore été généré
if (!isset($_SESSION['verification_code'])) {
    // Si le code de vérification n'existe pas encore, on en génère un aléatoire à 6 chiffres
    $_SESSION['verification_code'] = random_int(100000, 999999); 

    // Récupère l'email de l'utilisateur connecté à partir de la session
    $email = $_SESSION['user']['email'];

    // Création du contenu de l'email
    $subject = "Your Verification Code"; // Sujet de l'email
    // Corps de l'email, intégration du prénom de l'utilisateur et du code de vérification
    $body = "<p>Hello {$_SESSION['user']['prenom']},</p>";
    $body .= "<p>Your verification code is: <strong>{$_SESSION['verification_code']}</strong></p>";
    $body .= "<p>If you did not request this, please ignore this email.</p>";

    // Création d'une instance de la classe MailSender et envoi de l'email
    $mailer = new MailSender();
    if (!$mailer->sendMail($email, $subject, $body)) {
        // Si l'email n'a pas pu être envoyé, afficher un message d'erreur et quitter le script
        echo "Failed to send verification email. Please contact support.";
        exit(); // Interrompre le script en cas d'échec d'envoi
    }
}

$message = ""; // Variable pour stocker les messages de succès ou d'erreur

// Vérification si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère le code de vérification soumis par l'utilisateur via le formulaire
    $input_code = htmlspecialchars($_POST['code']); // Utilisation de htmlspecialchars pour prévenir les attaques XSS

    // Vérifie si le code soumis par l'utilisateur correspond au code de vérification stocké dans la session
    if ($input_code == $_SESSION['verification_code']) {
        // Si le code est correct, vérifier le rôle de l'utilisateur
        if ($_SESSION['user']['role'] === 'admin') {
            // Si l'utilisateur est un administrateur, redirige vers la page d'administration
            header("Location: admin.php");
        } else {
            // Sinon, redirige l'utilisateur vers la page d'accueil
            header("Location: home.php");
        }
        exit(); // Terminer le script après la redirection
    } else {
        // Si le code ne correspond pas, afficher un message d'erreur
        $message = "The verification code does not match. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f8ff; /* Bleu clair */
            color: #000; /* Couleur du texte noir */
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .card {
            background-color: #ffffff; /* Blanc */
            border: 1px solid #007bff; /* Bordure bleue */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Légère ombre */
            width: 100%;
            max-width: 500px;
            padding: 20px;
        }

        .card-header {
            background-color: #007bff; /* Bleu principal */
            color: #ffffff; /* Texte blanc */
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            padding: 10px;
            border-radius: 8px 8px 0 0; /* Coins arrondis en haut */
        }

        .card-body {
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff; /* Bouton bleu */
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Bleu plus foncé au survol */
            border-color: #0056b3;
        }

        a {
            color: #007bff; /* Lien bleu */
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .alert-danger {
            background-color: #f8d7da; /* Rouge clair */
            color: #842029; /* Rouge foncé */
            border-color: #f5c2c7;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="card-header">
        <h3>Verification</h3>
    </div>
    <div class="card-body">
        <?php if ($message): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <p class="text-center">
            A verification code has been sent to your email: <strong><?= htmlspecialchars($_SESSION['user']['email']); ?></strong>.
        </p>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="code" class="form-label">Enter Verification Code</label>
                <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Verify</button>
            </div>
        </form>
    </div>
    <div class="card-footer text-center">
        <a href="login.php" class="btn btn-link">Back to Login</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

