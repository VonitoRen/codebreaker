<?php
session_start();

if (!isset($_SESSION['username'])) {
    // Redirect to name input page if username is not set
    header('Location: index.php');
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codebreaker Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet"> <!-- Arcade-style font -->

    <style>
html, body {
    height: 100%;
    margin: 0;
}

body {
    font-family: 'Press Start 2P', cursive; /* Arcade-style font */
    background: radial-gradient(circle at center, #000 0%, #333 100%); /* Arcade-style black background with gradient */
    color: #0f0; /* Neon green text */
    display: flex;
    align-items: center;
    justify-content: center;
}

.container {
    text-align: center;
    max-width: 800px; /* Increase width */
    width: 100%;
    height: 80%; /* Increase height */
    padding: 30px; /* Add padding for better spacing */
    background-color: rgba(0, 0, 0, 0.8); /* Dark background with some transparency */
    border-radius: 10px; /* Optional: Rounded corners */
    box-shadow: 0 0 20px rgba(0, 255, 0, 0.8); /* Neon glow */
    display: flex;
    flex-direction: column;
    justify-content: center; /* Center content vertically */
}

#timer {
    margin-top: 20px;
    font-size: 1.5em;
    font-weight: bold;
    text-shadow: 0 0 10px #0f0, 0 0 20px #0f0, 0 0 30px #0f0; /* Neon glow effect */
}

.digit-input {
    width: 60px; /* Increase width */
    height: 60px; /* Increase height */
    text-align: center;
    font-size: 2.5em; /* Increase font size */
    margin-right: 10px;
    background-color: #222; /* Dark input background */
    color: #0f0; /* Neon green text */
    border: 2px solid #0f0; /* Neon green border */
    border-radius: 8px; /* Rounded corners */
    box-shadow: 0 0 5px #0f0; /* Neon glow */
}

.digit-input.correct {
    background-color: #0f0; /* Neon green */
    color: #000; /* Black text for contrast */
}

.digit-input.present {
    background-color: #ff0; /* Yellow */
    color: #000; /* Black text for contrast */
}

.digit-input.incorrect {
    background-color: #fff; /* White */
    color: #000; /* Black text for contrast */
}

.btn-primary {
    background-color: #ff007f; /* Pink for arcade feel */
    border-color: #ff007f;
    box-shadow: 0 0 10px #ff007f; /* Neon glow */
}

.btn-primary:hover {
    background-color: #e60073; /* Darker pink for hover */
    border-color: #e60073;
}

.btn-danger {
    background-color: #ff0000; /* Red for restart */
    border-color: #ff0000;
    box-shadow: 0 0 10px #ff0000; /* Neon glow */
}

.btn-danger:hover {
    background-color: #e60000; /* Darker red for hover */
    border-color: #e60000;
}

#message {
    font-size: 1.2em;
    font-weight: bold;
    margin-top: 20px;
    text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; /* Neon red glow */
}

.form-control {
    font-family: 'Press Start 2P', cursive; /* Arcade-style font */
}

.modal-content {
    background-color: #222; /* Dark background for modal */
    color: #0f0; /* Neon green text */
    border-radius: 0; /* Sharp corners for arcade feel */
}

.modal-header {
    border-bottom: 1px solid #0f0; /* Neon green border for modal header */
}

.modal-footer {
    border-top: 1px solid #0f0; /* Neon green border for modal footer */
}

.btn-success {
    background-color: #0f0; /* Neon green for submit results button */
    border-color: #0f0;
    box-shadow: 0 0 10px #0f0; /* Neon glow */
}

.btn-success:hover {
    background-color: #0c0; /* Darker green for hover */
    border-color: #0c0;
}
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <p>Guess the code!</p>

        <div class="row justify-content-center mb-4">
            <div class="col-md-4">
                <div class="d-flex justify-content-center mb-2">
                    <input type="text" id="digit1" maxlength="1" class="form-control digit-input" placeholder="0" autocomplete="off">
                    <input type="text" id="digit2" maxlength="1" class="form-control digit-input" placeholder="0" autocomplete="off">
                    <input type="text" id="digit3" maxlength="1" class="form-control digit-input" placeholder="0" autocomplete="off">
                    <input type="text" id="digit4" maxlength="1" class="form-control digit-input" placeholder="0" autocomplete="off">
                </div>
                <button id="submitGuessBtn" class="btn btn-primary w-100 mb-2">Submit Guess</button>
                <button id="restartBtn" class="btn btn-danger w-100">Restart Game</button>
            </div>
        </div>

        <div id="timer">Time Left: <span id="time">60</span> seconds</div>
        <div id="message" class="text-primary mb-5"></div>

        <form id="gameResultsForm" method="POST" action="save_results.php">
            <input type="hidden" name="attempts" id="attempts" value="0">
            <input type="hidden" name="time_completed" id="time_completed" value="0">
            <button id="submitGameResults" class="btn btn-success" style="display: none;">Submit Results</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let countdown;
        let timeLeft = 60;  // 60 seconds for the game
        let attempts = 0;

        function startTimer() {
            timeLeft = 60;
            $('#time').text(timeLeft);

            countdown = setInterval(() => {
                timeLeft--;
                $('#time').text(timeLeft);

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    gameOver();
                }
            }, 1000);  // Update every second
        }

        function gameOver() {
            $('#message').text("Time's up! You failed to break the code.").addClass('text-danger');
            $('.digit-input').prop('disabled', true);
            $('#submitGuessBtn').prop('disabled', true);
            $('#submitGameResults').show(); // Show results submission button

            // Set the time completed and number of attempts
            $('#time_completed').val(60 - timeLeft);
            $('#attempts').val(attempts);
        }

        $('.digit-input').on('keyup', function (e) {
            let nextInput = $(this).next('.digit-input');
            if (this.value.length == 1 && nextInput.length > 0) {
                nextInput.focus();
            }
        });

        $('.digit-input').on('focus', function() {
            $(this).select();  // Select the text in the input field
        });

        function checkGuess(guess) {
            let guessArr = guess.split('');
            let secretArr = "<?php echo $_SESSION['code']; ?>".split('');

            $('.digit-input').removeClass('correct present incorrect');

            for (let i = 0; i < guessArr.length; i++) {
                if (guessArr[i] === secretArr[i]) {
                    $(`#digit${i+1}`).addClass('correct');
                } else if (secretArr.includes(guessArr[i])) {
                    $(`#digit${i+1}`).addClass('present');
                } else {
                    $(`#digit${i+1}`).addClass('incorrect');
                }
            }
        }

        function submitGuess() {
            let guess = $('#digit1').val() + $('#digit2').val() + $('#digit3').val() + $('#digit4').val();

            if (guess.length !== 4 || isNaN(guess)) {
                alert("Please enter a valid 4-digit number");
                return;
            }

            attempts++; // Increment attempts on each guess

            $.ajax({
                type: 'POST',
                url: 'codebreaker.php',
                data: { guess: guess },
                success: function(response) {
                    let data = JSON.parse(response);
                    $('#message').html(data.message).removeClass('text-danger');

                    if (data.status === 'win') {
                        clearInterval(countdown); // Stop timer if the player wins
                        $('.digit-input').prop('disabled', true);
                        $('#submitGuessBtn').prop('disabled', true);
                        $('#submitGameResults').show(); // Show results submission button

                        // Set the time completed and number of attempts
                        $('#time_completed').val(60 - timeLeft);
                        $('#attempts').val(attempts);
                    } else {
                        checkGuess(guess);
                    }
                }
            });
        }

        function restartGame() {
            $.ajax({
                type: 'POST',
                url: 'codebreaker.php',
                data: { restart: true },
                success: function(response) {
                    let data = JSON.parse(response);
                    $('#message').html(data.message).removeClass('text-danger text-success');
                    $('.digit-input').prop('disabled', false).val('');
                    $('#submitGuessBtn').prop('disabled', false);
                    $('#submitGameResults').hide(); // Hide results submission button
                    startTimer();  // Restart the timer
                    $('#digit1').focus();  // Focus on the first input field
                    $('.digit-input').removeClass('correct present incorrect'); // Reset colors

                    // Reset attempts counter
                    attempts = 0;
                }
            });
        }

        $(document).ready(function() {
            startTimer();

            $('#submitGuessBtn').on('click', function() {
                submitGuess();
            });

            $('#restartBtn').on('click', function() {
                restartGame();
            });
        });
    </script>
</body>
</html>