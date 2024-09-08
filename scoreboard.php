<?php
session_start();
require 'db_connection.php'; // Ensure this points to your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Prepare and execute the SQL query
    $sql = "SELECT username, attempts, time_completed
    FROM game_results
    ORDER BY attempts ASC, time_completed ASC
    LIMIT 5;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    // Fetch all results
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return JSON data
    header('Content-Type: application/json');
    echo json_encode($data);
}
?>

