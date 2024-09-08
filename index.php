<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Save the username in the session
    $_SESSION['username'] = $_POST['username'];

    // Generate a random code for the game (for demonstration purposes)
    $_SESSION['code'] = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);

    // Redirect to the game page
    header('Location: game.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Your Name</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet"> <!-- Arcade-style font -->
    <link rel="stylesheet" href="style.css">
    <style>

        .form-control, .btn {
            font-family: 'Press Start 2P', cursive;
        }
        .form-control {
            text-align: center; /* Center text inside the input field */
        }
        .form-control:focus {
            text-align: center; /* Ensure text remains centered when focused */
            background-color: #333; /* Optional: Change background color on focus */
            color: #0f0; /* Optional: Change text color on focus */
            border-color: #555; /* Optional: Change border color on focus */
        }
        .scoreboard {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background: #222;
            color: #fff;
            border-radius: 8px;
        }
        table {
            width: 100%;
            color: #fff;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #444;
        }
        th {
            background-color: #333;
        }
        td {
            background-color: #222;
        }
        table tbody tr:hover {
            background-color: #444;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="row text-center mb-4">
                <p class="balangay-text">CODE BREAKER!!!</p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h1>Enter Your Name to Start</h1>
                    <form method="POST" action="">
                        <div class="mb-3">
       
                            <input type="text" class="form-control mb-5 mt-2" id="username" name="username" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Start Game</button>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="scoreboard">
                        <h2>Scoreboard</h2>
                        <table id="scoreboardTable">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Attempts</th>
                                    <th>Time Completed</th>
                                </tr>
                            </thead>
                            <tbody class="scoreboard-list">
                                <!-- Data will be inserted here by AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
            $.ajax({
                url: 'scoreboard.php',
                type: 'post',
                data: {},
                dataType: 'json',
                success: function(response){
                    var $scoreboardList = $('#scoreboardTable tbody');
                    $scoreboardList.empty(); // Clear existing entries

                    if (response.length > 0) {
                        $.each(response, function(index, record){
                            var listItem = '<tr>' +
                                '<td>' + record.username + '</td>' +
                                '<td>' + record.attempts + '</td>' +
                                '<td>' + record.time_completed + ' seconds</td>' +
                                '</tr>';
                            $scoreboardList.append(listItem);
                        });
                    } else {
                        $scoreboardList.append('<tr><td colspan="3">No records found.</td></tr>');
                    }
                },
                error: function(xhr, status, error){
                    console.log("Status: " + status);
                    console.log("Error: " + error);
                    console.log("XHR: ", xhr);
                }
            });
        });
    </script>
</body>
</html>