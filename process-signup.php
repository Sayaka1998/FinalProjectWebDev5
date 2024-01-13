<?php

//Function to validate user input
function validateInput($name, $email, $password, $passwordConfirmation) {
    //Check if any of the required fields is empty
    if (empty($name) || empty($email) || empty($password) || empty($passwordConfirmation)) {
        die("All fields are required");
    }

    //Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    //Validate password
    if (strlen($password) < 8 || !preg_match("/[a-z]/i", $password) || !preg_match("/[0-9]/", $password)) {
        die("Password must be at least 8 characters and contain at least one letter and one number");
    }

    //Check if password and password match
    if ($password !== $passwordConfirmation) {
        die("Passwords do not match");
    }
}

//Function to insert user into the database
function insertUser($name, $email, $password, $mysqli) {
    //Hash the password using bcrypt (saw a guy explaining why we should use)
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    //Query to insert user into the database
    $sql = "INSERT INTO user (name, email, password_hash) VALUES (?, ?, ?)";
    $stmt = $mysqli->prepare($sql);

    //Check if the SQL query was successful
    if (!$stmt) {
        die("SQL error: " . $mysqli->error);
    }

    //Bind parameters and execute
    $stmt->bind_param("sss", $name, $email, $passwordHash);

    if ($stmt->execute()) {
        //Redirect user to a success page /success-signup.php or html
        header("Location: ");
        exit;
    } else {
        //Check if the email is taken
        if ($stmt->errno === 1062) {
            die("Email already taken");
        } else {
            die("Registration failed. Please try again later.");
        }
    }
}

//Include database file /database.php (depends of what is the name of our database)
$mysqli = require __DIR__ . " ";

//Form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    //Get user input in the form
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $passwordConfirmation = $_POST["password_confirmation"];

    //Authenticate user input
    validateInput($name, $email, $password, $passwordConfirmation);

    //Insert user into the database
    insertUser($name, $email, $password, $mysqli);
}
?>
