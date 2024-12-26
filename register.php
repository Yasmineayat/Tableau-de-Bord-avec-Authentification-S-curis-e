<?php
require_once 'db.php';  // Inclut le fichier db.php pour initialiser la connexion à la base de données
require_once 'Utilisateur.php';  // Inclut le fichier Utilisateur.php qui contient la classe Utilisateur

$message = "";  // Variable pour stocker les messages d'erreur ou de succès

// Initialise la connexion à la base de données
$database = new Database();  // Crée une instance de la classe Database
$db = $database->getConnection();  // Récupère une connexion à la base de données via la méthode getConnection()
$utilisateur = new Utilisateur($db);  // Crée une instance de la classe Utilisateur, en passant la connexion à la base de données

// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Si le formulaire a été soumis, les données sont traitées ici

    // Récupère les valeurs soumises et les sécurise en utilisant htmlspecialchars pour éviter les attaques XSS
    $utilisateur->nom = htmlspecialchars($_POST['nom']);  // Récupère et sécurise le champ 'nom' du formulaire
    $utilisateur->prenom = htmlspecialchars($_POST['prenom']);  // Récupère et sécurise le champ 'prenom' du formulaire
    $utilisateur->email = htmlspecialchars($_POST['email']);  // Récupère et sécurise le champ 'email' du formulaire
    $utilisateur->password = htmlspecialchars($_POST['password']);  // Récupère et sécurise le champ 'password' du formulaire
    $utilisateur->role = htmlspecialchars($_POST['role']);  // Récupère et sécurise le champ 'role' du formulaire

    // Appelle la méthode 'register' de la classe Utilisateur pour enregistrer l'utilisateur dans la base de données
    $result = $utilisateur->register();  

    // Vérifie si l'inscription a réussi
    if ($result === true) {  
        // Si l'enregistrement est réussi, redirige l'utilisateur vers la page de connexion
        header("Location: login.php");  // Redirige vers la page login.php
        exit();  // Arrête l'exécution du script après la redirection
    } else {
        // Si une erreur se produit, le message d'erreur est stocké dans la variable $message
        $message = $result;  // Affiche l'erreur retournée par la méthode 'register'
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
    background-image: url('https://i.pinimg.com/236x/83/ce/e9/83cee9d479ef2b4012550302ef630255.jpg');
    background-size: center;
    background-position: cover;
    background-repeat: cover;
    height: 100vh;
    filter: brightness(1.2); /* Augmente la luminosité de l'image */
}

        .card {
            background: rgba(255, 255, 255, 0.9); /* Semi-transparent white background */
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2); /* Slight shadow for the card */
        }
    </style>
</head>
<body>
<div class="container h-100 d-flex align-items-center justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header text-center">
                <h3>VEUILLIEZ REMPLIR TOUTES LES CHAMPS</h3>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prenom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center">
                <a href="login.php" class="btn btn-link">Already have an account? Login</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
