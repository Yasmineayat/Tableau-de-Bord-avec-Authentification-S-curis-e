<?php
session_start();
require_once 'db.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

try {
    // Fetch tasks for the logged-in user
    $user_id = $_SESSION['user']['id'];
    $query = $db->prepare("SELECT nom, description, date_time FROM taches WHERE id_utilisateur = :user_id");
    $query->execute(['user_id' => $user_id]);
    $tasks = $query->fetchAll(PDO::FETCH_ASSOC);

    // Generate the .ics content
    $icalendar = "BEGIN:VCALENDAR\r\n";
    $icalendar .= "VERSION:2.0\r\n";
    $icalendar .= "PRODID:-//YourApp//Task Export//EN\r\n";

    foreach ($tasks as $task) {
        $icalendar .= "BEGIN:VEVENT\r\n";
        $icalendar .= "SUMMARY:" . htmlspecialchars($task['nom']) . "\r\n";
        $icalendar .= "DESCRIPTION:" . htmlspecialchars($task['description']) . "\r\n";

        // Ensure valid date formatting
        $start_date = date('Ymd\THis\Z', strtotime($task['date_time_start']));
        $end_date = date('Ymd\THis\Z', strtotime($task['date_time_end']));

        $icalendar .= "DTSTART:" . $start_date . "\r\n";
        $icalendar .= "DTEND:" . $end_date . "\r\n";
        $icalendar .= "END:VEVENT\r\n";
    }

    $icalendar .= "END:VCALENDAR\r\n";

    // Output the .ics file for download
    header('Content-Type: text/calendar; charset=utf-8');
    header('Content-Disposition: attachment; filename="tasks.ics"');

    echo $icalendar;
    exit;

} catch (Exception $e) {
    // Handle errors (e.g., database errors)
    echo "Error: " . $e->getMessage();
    exit();
}
?>
