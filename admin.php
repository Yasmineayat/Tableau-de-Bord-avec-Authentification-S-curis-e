<?php
session_start();
require_once 'db.php';
require_once 'Utilisateur.php';

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

$message = "";

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $userId = $_GET['delete_user'];
    $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $message = "User deleted successfully.";
    } else {
        $message = "Failed to delete user.";
    }
}

// Handle user update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $userId = $_POST['user_id'];
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $email = htmlspecialchars($_POST['email']);
    $role = htmlspecialchars($_POST['role']);

    $stmt = $db->prepare("UPDATE utilisateurs SET nom = :nom, prenom = :prenom, email = :email, role = :role WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->bindParam(':nom', $nom);
    $stmt->bindParam(':prenom', $prenom);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':role', $role);

    if ($stmt->execute()) {
        $message = "User updated successfully.";
    } else {
        $message = "Failed to update user.";
    }
}

// Get total number of users
$userQuery = $db->query("SELECT COUNT(*) as count FROM utilisateurs");
$userCount = $userQuery->fetch(PDO::FETCH_ASSOC)['count'];

// Get total number of tasks
$taskQuery = $db->query("SELECT COUNT(*) as count FROM taches");
$taskCount = $taskQuery->fetch(PDO::FETCH_ASSOC)['count'];

// Get all users
$userListQuery = $db->query("SELECT * FROM utilisateurs");
$users = $userListQuery->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <a href="export_ical.php" class="btn btn-primary">Export Tasks to iCalendar</a>

</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin Dashboard</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="row mb-4">
        <!-- Card for Number of Users -->
        <div class="col-md-6">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4"><?= htmlspecialchars($userCount); ?></p>
                </div>
            </div>
        </div>
        <!-- Card for Number of Tasks -->
        <div class="col-md-6">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Total Tasks</h5>
                    <p class="card-text display-4"><?= htmlspecialchars($taskCount); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Table of Users -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Users</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['nom']); ?></td>
                        <td><?= htmlspecialchars($user['prenom']); ?></td>
                        <td><?= htmlspecialchars($user['email']); ?></td>
                        <td><?= htmlspecialchars($user['role']); ?></td>
                        <td>
                            <!-- Delete User -->
                            <a href="?delete_user=<?= $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>

                            <!-- Edit User -->
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id']; ?>">Edit</button>
                        </td>
                    </tr>

                    <!-- Edit User Modal -->
                    <div class="modal fade" id="editUserModal<?= $user['id']; ?>" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <form method="POST" action="">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                                        <div class="mb-3">
                                            <label for="nom" class="form-label">Nom</label>
                                            <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($user['nom']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="prenom" class="form-label">Prenom</label>
                                            <input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($user['prenom']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role</label>
                                            <select name="role" class="form-control" required>
                                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" name="update_user" class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
