<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection file here
$mysqli = require __DIR__ . "/database.php";

// Function to hash the password
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Function to validate and sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

// Function to register a user
function registerUser($type, $firstname, $lastname, $email, $password, $mysqli) {
    // Validate and sanitize input
    $type = sanitizeInput($type);
    $firstname = sanitizeInput($firstname);
    $lastname = sanitizeInput($lastname);
    $email = sanitizeInput($email);
    $password = sanitizeInput($password);

    // Hash the password
    $hashedPassword = hashPassword($password);

    // Define an array to map user types to corresponding table names
    $tableNames = [
        'Customer' => 'customers_tb',
        'Staff' => 'staff_tb',
        // Add other types as needed
    ];

    // Check if the user type is valid
    if (!array_key_exists($type, $tableNames)) {
        die("Invalid user type");
    }

    // Use the correct table name based on user type
    $tableName = $tableNames[$type];

    // Check if the email already exists in the appropriate table
    $checkEmailQuery = "SELECT COUNT(*) AS count FROM " . $tableName . " WHERE email = ?";
    $stmtCheckEmail = $mysqli->prepare($checkEmailQuery);

    if (!$stmtCheckEmail) {
        die("SQL error: " . $mysqli->error);
    }

    $stmtCheckEmail->bind_param("s", $email);
    $stmtCheckEmail->execute();

    $result = $stmtCheckEmail->get_result();
    $emailCount = $result->fetch_assoc()["count"];

    // If the email doesn't exist, proceed with registration
    if ($emailCount == 0) {
        // Insert user data into the appropriate table
        $insertQuery = "INSERT INTO " . $tableName . " (fname, lname, email, pass, type) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $mysqli->prepare($insertQuery);

        if (!$stmtInsert) {
            die("SQL error: " . $mysqli->error);
        }

        $stmtInsert->bind_param("sssss", $firstname, $lastname, $email, $hashedPassword, $type);

        if ($stmtInsert->execute()) {
            // If the user is a staff, add an entry to the approval_tb table
            if ($type === "Staff") {
                $approvalQuery = "INSERT INTO approval_tb (sid, status) VALUES (LAST_INSERT_ID(), 'pending')";
                $stmtApproval = $mysqli->prepare($approvalQuery);

                if (!$stmtApproval) {
                    die("SQL error: " . $mysqli->error);
                }

                $stmtApproval->execute();
            }

            // Registration successful
            echo "Registration successful!";
        } else {
            die("Registration failed: " . $mysqli->error);
        }
    } else {
        // Email already exists
        echo "Email already exists. Please choose a different email.";
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $type = $_POST["type"];
    $firstname = $_POST["fname"];
    $lastname = $_POST["lname"];
    $email = $_POST["email"];
    $password = $_POST["pass"];

    // Validate and register the user
    registerUser($type, $firstname, $lastname, $email, $password, $mysqli);
}
?>