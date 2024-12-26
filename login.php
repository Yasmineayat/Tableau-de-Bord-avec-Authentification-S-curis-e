<?php
session_start(); // Démarre la session. Cela permet de conserver des informations entre différentes pages, comme les données de l'utilisateur connecté.
require_once 'db.php'; // Inclut le fichier 'db.php' qui contient la logique de connexion à la base de données.
require_once 'Utilisateur.php'; // Inclut le fichier 'Utilisateur.php' qui contient la classe Utilisateur pour gérer les utilisateurs.

$message = ""; // Initialise une variable pour stocker les messages d'erreur ou de succès.


// Initialise la connexion à la base de données en instanciant l'objet Database.
$database = new Database();
// Appelle la méthode getConnection() pour récupérer l'objet de connexion à la base de données.
$db = $database->getConnection();
// Crée un objet de la classe Utilisateur avec la connexion à la base de données.
$utilisateur = new Utilisateur($db);

// Vérifie si le formulaire a été soumis via la méthode POST.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupère et assainit les données du formulaire pour éviter les attaques XSS.
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Attribue les valeurs du formulaire à l'objet Utilisateur.
    $utilisateur->email = $email;
    $utilisateur->password = $password;

    // Appelle la méthode getUser() de la classe Utilisateur pour vérifier si l'utilisateur existe dans la base de données avec l'email fourni.
    $user = $utilisateur->getUser($email);
    
    // Si l'utilisateur n'existe pas dans la base de données, affiche un message d'erreur.
    if (!$user) {
        $message = "Email does not exist. Please register."; // Message d'erreur lorsque l'email n'est pas trouvé.
    } else {
        // Si l'utilisateur existe, réaffecte le mot de passe pour la tentative de connexion.
        $utilisateur->password = $password; // Réassigne le mot de passe pour tenter la connexion.
        
        // Appelle la méthode login() de la classe Utilisateur pour vérifier si le mot de passe est correct.
        if ($utilisateur->login()) {
            // Si la connexion réussit, enregistre les informations de l'utilisateur dans la session.
            $_SESSION['user'] = [
                'id' => $utilisateur->id, // Enregistre l'ID de l'utilisateur dans la session.
                'nom' => $utilisateur->nom, // Enregistre le nom de l'utilisateur dans la session.
                'prenom' => $utilisateur->prenom, // Enregistre le prénom de l'utilisateur dans la session.
                'email' => $utilisateur->email, // Enregistre l'email de l'utilisateur dans la session.
                'role' => $utilisateur->role // Enregistre le rôle de l'utilisateur dans la session.
            ];
            // Redirige l'utilisateur vers la page de vérification avec un paramètre 'new' égal à 1.
            header("Location: verification.php?new=1");
            exit(); // Arrête l'exécution du script après la redirection.
        } else {
            // Si le mot de passe est incorrect, affiche un message d'erreur.
            $message = "Incorrect password. Please try again."; // Message d'erreur lorsque le mot de passe est incorrect.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>hello HONEY </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('https://i.pinimg.com/236x/8c/08/8a/8c088a9cbcb05a6ee5dae85f733ebc34.jpg');
            background-size: center;
            background-position: cover;
            background-repeat: center;
            height: 100vh;
        }
        .card {
            background: rgba(255, 255, 255, 0.9); /* Slightly transparent white background */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
<div class="container h-100 d-flex align-items-center justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h3>WELCOME HONEY TO YOUR DASHBOARD</h3>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="register.php" class="btn btn-link">Don't have an account? Register</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
