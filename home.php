<?php 
session_start();
require_once 'db.php';
require_once 'Tache.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Initialize database and Tache class
$database = new Database();
$db = $database->getConnection();
$tache = new Tache($db);

$user_id = $_SESSION['user']['id'];
$message = "";

// Handle form submission for adding a task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $tache->nom = htmlspecialchars($_POST['nom']);
    $tache->description = htmlspecialchars($_POST['description']);
    $tache->date_time = !empty($_POST['date_time']) ? htmlspecialchars($_POST['date_time']) : null;
    $tache->id_utilisateur = $user_id;

    if ($tache->create()) {
        $message = "Task added successfully.";
    } else {
        $message = "Failed to add task. Please try again.";
    }
}

// Handle delete task
if (isset($_GET['delete'])) {
    $tache->id = intval($_GET['delete']); // Sanitize ID

    if ($tache->delete()) {
        $message = "Task deleted successfully.";
    } else {
        $message = "Failed to delete task. Please try again.";
    }
}

// Handle update task
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_task'])) {
    $tache->id = intval($_POST['task_id']); // Sanitize ID
    $tache->nom = htmlspecialchars($_POST['nom']);
    $tache->description = htmlspecialchars($_POST['description']);
    $tache->date_time = !empty($_POST['date_time']) ? htmlspecialchars($_POST['date_time']) : null;
    $tache->id_utilisateur = $user_id;

    if ($tache->update()) {
        $message = "Task updated successfully.";
    } else {
        $message = "Failed to update task. Please try again.";
    }
}

// Fetch all tasks for the logged-in user
$query = "SELECT * FROM taches WHERE id_utilisateur = :id_utilisateur";
$stmt = $db->prepare($query);
$stmt->bindParam(':id_utilisateur', $user_id, PDO::PARAM_INT);
$stmt->execute();
$tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
body {
    background: linear-gradient(135deg, #007bff, #6610f2); /* Dégradé bleu-violet */
    font-family: 'Poppins', sans-serif; /* Police moderne et lisible */
    color: #fff; /* Texte blanc par défaut */
    margin: 0;
    padding: 0;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    background: #ffffff; /* Blanc pour le contenu principal */
    border-radius: 12px; /* Coins arrondis */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); /* Ombre douce */
    padding: 30px;
    max-width: 900px;
    width: 100%;
}

/* Header Styles */
h1 {
    font-size: 2.5rem;
    font-weight: bold;
    color: #007bff; /* Couleur bleu principal */
    text-shadow: 1px 2px 5px rgba(0, 0, 0, 0.2); /* Effet d'ombre légère */
}

.d-flex {
    margin-bottom: 20px;
    align-items: center;
    justify-content: space-between;
}

.btn-danger {
    background: linear-gradient(135deg, #e3342f, #dc3545); /* Dégradé rouge */
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    transition: transform 0.2s, background 0.3s;
}

.btn-danger:hover {
    transform: scale(1.05); /* Agrandissement au survol */
    background: linear-gradient(135deg, #dc3545, #c82333); /* Rouge plus foncé */
}

/* Form Styles */
form {
    margin-bottom: 20px;
}

.form-control {
    border-radius: 8px; /* Coins arrondis */
    border: 1px solid #ced4da;
    padding: 10px;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s;
}

.form-control:focus {
    border-color: #007bff; /* Couleur au focus */
    box-shadow: 0 0 8px rgba(0, 123, 255, 0.4); /* Effet lumineux */
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #6610f2); /* Dégradé bleu-violet */
    border: none;
    font-weight: bold;
    color: #fff;
    border-radius: 8px;
    transition: transform 0.2s, background 0.3s;
}

.btn-primary:hover {
    transform: scale(1.05); /* Agrandissement au survol */
    background: linear-gradient(135deg, #6610f2, #007bff); /* Inversion du dégradé */
}

/* Table Styles */
.table {
    background: #ffffff;
    border-radius: 10px; /* Coins arrondis */
    overflow: hidden;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1); /* Ombre douce */
}

.table th {
    background: #007bff; /* En-tête bleu */
    color: #fff;
    text-transform: uppercase;
    padding: 15px;
    font-weight: bold;
}

.table td {
    padding: 10px;
    color: #333;
}

.table tbody tr:hover {
    background: rgba(0, 123, 255, 0.1); /* Surbrillance au survol */
    transition: background 0.3s;
}

/* Modal Styles */
.modal-content {
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3); /* Ombre pour plus de style */
}

.modal-header {
    background: #6610f2; /* Violet foncé */
    color: #fff;
    border-bottom: none;
}

.modal-footer {
    border-top: none;
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14); /* Dégradé jaune-orange */
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    transition: transform 0.2s, background 0.3s;
}

.btn-warning:hover {
    transform: scale(1.05); /* Agrandissement au survol */
    background: linear-gradient(135deg, #fd7e14, #ffc107); /* Inversion du dégradé */
}

.btn-success {
    background: linear-gradient(135deg, #28a745, #218838); /* Dégradé vert */
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 8px;
    transition: transform 0.2s, background 0.3s;
}

.btn-success:hover {
    transform: scale(1.05); /* Agrandissement au survol */
    background: linear-gradient(135deg, #218838, #28a745); /* Inversion du dégradé */
}

</style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>
        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info">
            <?= htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Add Task Form -->
    <form method="POST" action="" class="mb-4">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="text" name="nom" class="form-control" placeholder="Task Name" required>
            </div>
            <div class="col-md-4">
                <input type="text" name="description" class="form-control" placeholder="Task Description" required>
            </div>
            <div class="col-md-3">
                <input type="datetime-local" name="date_time" class="form-control" placeholder="Task date_time"required>
            </div>
            <div class="col-md-2">
                <button type="submit" name="add_task" class="btn btn-primary w-100">Add Task</button>
            </div>
        </div>
    </form>

    <!-- Task List -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Date & Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
    <td><?= htmlspecialchars($task['nom']); ?></td>
    <td><?= htmlspecialchars($task['description']); ?></td>
    <td><?= isset($task['date_time']) ? htmlspecialchars($task['date_time']) : 'No date provided'; ?></td>
    <td>
        <!-- Edit Task -->
        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editTaskModal<?= $task['id']; ?>">Edit</button>

        <!-- Delete Task -->
        <a href="?delete=<?= $task['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a>
    </td>
</tr>

                <!-- Edit Task Modal -->
                <div class="modal fade" id="editTaskModal<?= $task['id']; ?>" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <form method="POST" action="">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="task_id" value="<?= $task['id']; ?>">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Task Name</label>
                                        <input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($task['nom']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Task Description</label>
                                        <input type="text" name="description" class="form-control" value="<?= htmlspecialchars($task['description']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="date_time" class="form-label">Date & Time</label>
                                        <input type="datetime-local" name="date_time" class="form-control" required>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <button type="submit" name="update_task" class="btn btn-success">Update Task</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
