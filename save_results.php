<?php
session_start();
require 'db_connection.php'; // Ensure this points to your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $attempts = $_POST['attempts'];
    $timeCompleted = $_POST['time_completed'];

    // Save to database
    $stmt = $pdo->prepare("INSERT INTO game_results (username, attempts, time_completed) VALUES (?, ?, ?)");
    $stmt->execute([$username, $attempts, $timeCompleted]);

    // Clear session and redirect to index.php to input a new name
    session_destroy();
    header('Location: index.php');
    exit();
}
?>