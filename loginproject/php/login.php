<?php
session_start();

// Initialize a message variable
$message = "";
$blockStaffLogin = false;

// Check if the form is submitted and if the "email" key exists in $_POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["email"])) {
    // Replace 'localhost', 'root', '', and 'customer_tb' with your actual database connection details
    $conn = new mysqli('localhost', 'root', '', 'customer_tb');

    // Check if the "password" key exists in $_POST
    if (isset($_POST["password"])) {
        // Query to check if the entered email and password match a record in the database
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='" . $_POST["email"] . "' AND user_type='" . $_POST["type"] . "'");

        // Fetch the result as an associative array
        $row = mysqli_fetch_array($result);

        // Check if a record with the provided credentials exists
        if (is_array($row)) {
            // Check if the account is locked due to too many unsuccessful login attempts
            if ($row['login_attempts'] < 5) {
                // Verify the password
                if ($_POST["password"] === $row["password_hash"]) {
                    // Password is correct, reset login attempts
                    mysqli_query($conn, "UPDATE users SET login_attempts = 0 WHERE email='" . $_POST["email"] . "'");

                    // Check if the user is staff
                    if ($_POST["type"] === "staff") {
                        // Block the staff user
                        $blockStaffLogin = true;
                    } else {
                        // Set session variables for user ID, email, and set timestamp for the last activity (login time)
                        $_SESSION["id"] = $row['id'];
                        $_SESSION["email"] = $row['email'];
                        $_SESSION["last_activity"] = time();

                        // Add your code here for actions after a successful login
                        // For example, you can redirect to a welcome page
                        header("Location: welcome.php");
                        exit(); // Ensure that no further code is executed after the redirect
                    }
                } else {
                    // Password is incorrect, increment login attempts
                    mysqli_query($conn, "UPDATE users SET login_attempts = login_attempts + 1 WHERE email='" . $_POST["email"] . "'");
                    $remaining_attempts = 5 - $row['login_attempts'];
                    $message = "Invalid Password! Remaining attempts: {$remaining_attempts}";
                }
            } else {
                // Account is locked
                $message = "Account is locked due to too many unsuccessful login attempts. Please try again later.";
            }
        } else {
            // If no matching record is found, set an error message
            $message = "Invalid Email or Type!";
        }
    }
}

// If a user is logged in, check for session timeout and redirect to the login page if inactive
if (isset($_SESSION["id"])) {
    $timeout_duration = 1800; // 30 minutes in seconds

    // Check if the last activity time is set
    if (isset($_SESSION["last_activity"])) {
        $inactive_duration = time() - $_SESSION["last_activity"];

        // Check if the user has been inactive for more than the specified timeout duration
        if ($inactive_duration >= $timeout_duration) {
            // Session has timed out, destroy the session and redirect to login
            session_unset();
            session_destroy();
            header("Location: login.php");
            exit(); // Ensure that no further code is executed after the redirect
        } else {
            // Update the last activity timestamp
            $_SESSION["last_activity"] = time();
        }
    }
}

// Block the page if staff login is not allowed
if ($blockStaffLogin) {
    die("Staff login is not allowed on this page.");
}
?>
<html>
<head>
    <title>User Login</title>
</head>
<body>
    <form name="frmUser" method="post" action="" align="center">
        <!-- Display the error message if any -->
        <div class="message"><?php if ($message != "") { echo $message; } ?></div>

        <h3 align="center">Enter Login Details</h3>
        <!-- Input fields for email, password, and type -->
        Email:<br>
        <input type="text" name="email">
        <br>
        Password:<br>
        <input type="password" name="password">
        <br>
        Type:<br>
        <select name="type">
            <option value="customer">Customer</option>
            <option value="staff">Staff</option>
            <option value="admin">Admin</option>
        </select>
        <br><br>
        <!-- Submit and reset buttons for the form -->
        <input type="submit" name="submit" value="Submit">
        <input type="reset">
    </form>
</body>
</html>