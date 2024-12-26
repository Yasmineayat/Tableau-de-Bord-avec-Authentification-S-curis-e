<?php
session_start();  // Démarre une session PHP ou reprend une session existante

// Vérifie si l'utilisateur est connecté en vérifiant l'existence de 'user' dans la session
if (!isset($_SESSION['user'])) {  
    // Si l'utilisateur n'est pas connecté, redirige vers la page de connexion
    header("Location: login.php");
    exit();  // Arrête l'exécution du script après la redirection
}

// Récupère les détails de l'utilisateur à partir de la session (ces informations sont stockées lors de la connexion)
$user = $_SESSION['user'];  

// Vérifie si le formulaire a été soumis via la méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {  
    // Si le formulaire est soumis, les informations doivent être traitées

    // Récupère les nouvelles valeurs soumises dans le formulaire et les protège contre les attaques XSS
    $new_nom = htmlspecialchars($_POST['nom']);  // Récupère et sécurise le champ 'nom'
    $new_prenom = htmlspecialchars($_POST['prenom']);  // Récupère et sécurise le champ 'prenom'
    $new_email = htmlspecialchars($_POST['email']);  // Récupère et sécurise le champ 'email'
    
    // Si nécessaire, une fonctionnalité de changement de mot de passe peut être ajoutée ici :
    // $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash le mot de passe pour sécurité
    
    // Met à jour les informations de l'utilisateur dans la session avec les nouvelles valeurs
    $_SESSION['user']['nom'] = $new_nom;  // Met à jour le nom de l'utilisateur dans la session
    $_SESSION['user']['prenom'] = $new_prenom;  // Met à jour le prénom de l'utilisateur dans la session
    $_SESSION['user']['email'] = $new_email;  // Met à jour l'email de l'utilisateur dans la session
    
    // Optionnellement, ici vous devriez aussi mettre à jour les informations dans la base de données

    // Définit un message de succès qui peut être affiché dans l'interface utilisateur
    $message = "Profile updated successfully!";  
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Roboto', sans-serif;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            text-align: center;
            padding: 20px;
            font-size: 1.8rem;
            font-weight: bold;
        }

        .card-body {
            padding: 30px;
            background-color: #fff;
        }

        .form-label {
            font-weight: bold;
            color: #555;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 10px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #0056b3, #003f7f);
        }

        .btn-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .btn-link:hover {
            color: #0056b3;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .card-footer {
            background-color: #f4f6f9;
            text-align: center;
            padding: 20px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h3>Profile Information</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?= htmlspecialchars($message); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user['nom']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="prenom" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user['prenom']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">New Password (Optional)</label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="form-text text-muted">Leave empty if you do not want to change the password.</small>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="home.php" class="btn btn-link">Back to Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
