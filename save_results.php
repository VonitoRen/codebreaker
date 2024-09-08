<?php
session_start();
require 'db_connection.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $attempts = $_POST['attempts'];
    $timeCompleted = $_POST['time_completed'];

    
    $stmt = $pdo->prepare("INSERT INTO game_results (username, attempts, time_completed) VALUES (?, ?, ?)");
    $stmt->execute([$username, $attempts, $timeCompleted]);

    session_destroy();
    header('Location: index.php');
    exit();
}
?>