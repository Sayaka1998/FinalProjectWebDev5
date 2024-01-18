<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
require("./config.php");

// check if the request method is post
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SERVER["PATH_INFO"])) {
        // If a user is logged in, check for session timeout and redirect to the login page if inactive
        if (isset($_POST["sid"])) {
            session_id($_POST["sid"]);
            session_start();
            // Check if the last activity time is set
            if (isset($_SESSION["last_activity"]) && $_SESSION["last_activity"] > time()) {
                // Update the last activity timestamp
                $_SESSION["last_activity"] = time() + 600;
            } else {
                session_unset();
                session_destroy();
            }
        }

        switch ($_SERVER["PATH_INFO"]) {
            case "/login":
                $loginUser = null;
                $flag = true; // to check if the user type is staff
                $dbCon = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);
                if (!$dbCon) {
                    die("Connection error" . mysqli_connect_error());
                } else {
                    // Query to check if the entered email and user type match a record in the database
                    $result = mysqli_query($dbCon, "SELECT * FROM user_tb WHERE email='" . $_POST["email"] . "' AND type='" . $_POST["type"] . "'");
                    // Fetch the result as an associative array
                    $user = mysqli_fetch_array($result);
                    if ($user > 0) { // if the user data exist on the database
                        if ($_POST["type"] === "Staff") { // if the user data is staff
                            // check if the user data exists on the approval table
                            $resultAppr = mysqli_query($dbCon, "SELECT * FROM approval_tb WHERE uid = " . $user["uid"]);
                            if (mysqli_num_rows($resultAppr) > 0) { // if the user data is pending, the user can't log in.
                                $flag = false;
                            }
                        }
                        if ($flag) { // if the user type is not staff or the staff data is already approved
                            // check if the user data is in the black list
                            $resultBlk = mysqli_query($dbCon, "SELECT * FROM blacklst_tb WHERE uid = " . $user["uid"]);
                            if (mysqli_num_rows($resultBlk) > 0) { // if the user data exists in the black list, the user can't login.
                                $loginUser = 0;
                                echo "Account is locked due to too many unsuccessful login attempts. Please try again later.";
                            } else {
                                // Verify the enterd password and the hashed password on the user table
                                if (password_verify($_POST["pass"], $user["pass"])) {
                                    if ($user["ecount"] != 5) {
                                        // Password is correct, reset login attempts
                                        mysqli_query($dbCon, "UPDATE user_tb SET ecount = 5 WHERE uid=" . $user["uid"]);
                                    }
                                    // Set session variables for logged in user, and set timestamp for the last activity (login time)
                                    session_start();
                                    $_SESSION["loginUser"] = $user;
                                    $_SESSION["last_activity"] = time() + 600;
                                } else {
                                    $user["ecount"]--; // reduce the error count of password
                                    if ($user["ecount"] <= 0) { //Lock the user account after unsuccessful authentication attempts passes 5 times.
                                        mysqli_query($dbCon, "INSERT INTO blacklst_tb (uid) VALUES (" . $user["uid"] . ")");
                                    }
                                    // update the ecount on the user table 
                                    mysqli_query($dbCon, "UPDATE user_tb SET ecount=" . $user["ecount"] . " WHERE uid=" . $user["uid"]);
                                }
                            }
                        }
                    }
                }
                if (session_status() === 2) { // if session works, return the user type and session id to the front-end
                    $response = ["type" => $_SESSION["loginUser"]["type"], "sid" => session_id()];
                    echo json_encode($response);
                } else if ($loginUser === null && $flag === false) { // if the user type is staff and the data isn't approved
                    echo "Your data is not approved!";
                } else if ($loginUser === null) { // if the email/password/type is wrong.
                    echo "email/password/type is wrong.";
                }
                mysqli_close($dbCon);
                break;

            case "/logout":
                if (isset($_SESSION["loginUser"])) { // if the user is logged in, stop session
                    session_unset();
                    session_destroy();
                    echo "Log out";
                } else {
                    echo "Login first.";
                }
                break;

            case "/register":
                $type = $_POST["type"];
                $fname = $_POST["fname"];
                $lname = $_POST["lname"];
                $email = $_POST["email"];
                $pass = $_POST["pass"];

                $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                // Check if the email already exists in the appropriate table
                $checkEmailQuery = "SELECT email FROM user_tb WHERE email = ?";
                $stmtCheckEmail = $dbCon->prepare($checkEmailQuery);

                if (!$stmtCheckEmail) {
                    die("SQL error: " . $dbCon->error);
                }

                $stmtCheckEmail->bind_param("s", $email);
                $stmtCheckEmail->execute();

                $result = $stmtCheckEmail->get_result();
                $emailCount = $result->fetch_assoc()["email"];

                // If the email doesn't exist, proceed with registration
                if ($emailCount == 0) {
                    // Insert user data into the appropriate table
                    $insertQuery = "INSERT INTO user_tb (fname, lname, email, pass, type) VALUES (?, ?, ?, ?, ?)";
                    $stmtInsert = $dbCon->prepare($insertQuery);
                    if (!$stmtInsert) {
                        die("SQL error: " . $dbCon->error);
                    }
                    // hash password
                    $pass = password_hash($pass, PASSWORD_BCRYPT, ["cost" => 10]);
                    $stmtInsert->bind_param("sssss", $fname, $lname, $email, $pass, $type);

                    if ($stmtInsert->execute()) {
                        // If the user is a staff, add an entry to the approval table
                        if ($type === "Staff") {
                            // get the user id from the user table
                            $selectUid = "SELECT uid FROM user_tb WHERE email = '$email'";
                            $result = $dbCon->query($selectUid);

                            if ($result->num_rows > 0) {
                                $staffUid = $result->fetch_assoc()["uid"]; // set user id
                                $approvalQuery = "INSERT INTO approval_tb (uid, status) VALUES ('$staffUid', 'pending')";
                                $stmtApproval = $dbCon->query($approvalQuery);
                            } else {
                                echo "No data.";
                            }
                        }
                        // Registration successful
                        echo "Registration successful!";
                    } else {
                        die("Registration failed: " . $dbCon->error);
                    }
                } else {
                    // Email already exists
                    echo "Email already exists. Please choose a different email.";
                }
                break;

                // load the staff data which aren't approved yet
            case "/alist":
                if (isset($_SESSION["loginUser"])) { // if a user log in, start to connect to the approval table
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    // load the user data from the user table by using the user id on the approval table
                    $sql = "SELECT user_tb.uid,fname,lname,email,type FROM user_tb INNER JOIN approval_tb on user_tb.uid = approval_tb.uid";
                    $result = $dbCon->query($sql);

                    if ($result->num_rows > 0) { // if the approval table has the data
                        $pendingEmployees = array();
                        while ($row = $result->fetch_assoc()) { // set the data to the array
                            $pendingEmployees[] = $row;
                        }
                        echo json_encode($pendingEmployees);
                    } else {
                        echo "No staff awaiting"; // Return this message if there are no pending employees
                    }
                } else { // if a user doesn't log in, show the following message
                    echo "Login first.";
                }
                break;

                // approve staff
            case "/approve":
                // if the user is logged in and the user type is admin
                if (isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["type"] === "Admin") {
                    if (isset($_POST['approve'])) {
                        $staff = json_decode($_POST['approve'], true); // the user data which will be approved. it's converted to php object 
                        $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                        if ($dbCon->connect_error) {
                            echo "DB connection error. " . $dbCon->connect_error;
                            $dbCon->close();
                        } else {
                            // delete the user data from the approval table
                            $deleteSql = "DELETE FROM approval_tb WHERE uid = ?";
                            $deleteStmt = $dbCon->prepare($deleteSql);
                            $uid = $staff["uid"];
                            $deleteStmt->bind_param("i", $uid);
                            $deleteStmt->execute();
                            $dbCon->close();
                            echo "The staff is approved!";
                        }
                    } else {
                        echo "No staff selected.";
                    }
                } else {  // if a user doesn't log in, show the following message
                    echo "Login first.";
                }
                break;

            case "/blist":
                if (isset($_SESSION["loginUser"])) { // if the user s logged in, start to connect to the book table
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    if ($dbCon->connect_error) {
                        echo "DB connection error. " . $dbCon->connect_error;
                        $dbCon->close();
                    } else {
                        $loadBlist = "SELECT * FROM books_tb"; // load all of the data in the book table
                        $result = $dbCon->query($loadBlist);
                        if ($result->num_rows > 0) { // if the table has data, load each book data
                            $blist = [];
                            while ($book = $result->fetch_assoc()) {
                                array_push($blist, $book); // push each book data to a array
                            }
                            echo json_encode($blist); // convert the array to json, and send it to the front end
                        } else { // no data
                            echo "No books.";
                        }
                    }
                } else { // if a user doesn't log in, show the following message
                    echo "Login first.";
                }
                break;

            case "/borrow":
                if (isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["type"] === "Customer") { // if a user log in and the user type is customer, the user can borrow books.
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    if ($dbCon->connect_error) {
                        echo "DB connection error. " . $dbCon->connect_error;
                        $dbCon->close();
                    } else {
                        $bookBor = json_decode($_POST["book"]);
                        foreach ($bookBor as $book) {
                            // insert the data of the books borrowed to the lend table
                            $insLend = $dbCon->prepare("INSERT INTO lend_tb (isbn, uid, ldate,rdata) VALUES (?,?,?,?)");
                            $today = date("Y-m-d");
                            $returnDate  = date("Y-m-d", strtotime($today . '+1 week'));
                            $insLend->bind_param("siss", $book->isbn, $_SESSION["loginUser"]["uid"], $today, $returnDate);
                            $insLend->execute();
                            // update the status of the books borrowed
                            $updateLend = "UPDATE books_tb SET status = 'unavailable' WHERE isbn = $book->isbn";
                            $dbCon->query($updateLend);
                        }
                        $insLend->close();
                        $dbCon->close();
                        echo "Success to borrow books!";
                    }
                } else {  // if a user doesn't log in, show the following message
                    echo "Login first.";
                }
                break;

            case "/bookregister":
                // session_start();
                if (isset($_SESSION["loginUser"]) && ($_SESSION["loginUser"]["type"] == "Staff" || $_SESSION["loginUser"]["type"] == "Admin")) {

                    // Your existing code for book registration
                    $dbCon = mysqli_connect($dbServer, $dbUser, $dbPass, $dbName);
                    if (!$dbCon) {
                        die("Connection to DB failed! " . mysqli_connect_error());
                    } else {
                        // check if the book already exists
                        $bselectCmd = "SELECT isbn FROM books_tb WHERE isbn='" . $_POST["isbn"] . "'";
                        $result = $dbCon->query($bselectCmd);
                        if ($result->num_rows > 0) {
                            echo json_encode(["message" => "Registration failed!"]);
                        } else { // save the book data on the book table
                            $insCmd = $dbCon->prepare("INSERT INTO books_tb (isbn,bname,author,category,status) VALUES (?,?,?,?,?)");
                            $insCmd->bind_param("sssss", $_POST["isbn"], $_POST["bname"], $_POST["author"], $_POST["category"], $_POST["status"]);
                            if ($insCmd->execute()) {
                                echo json_encode(["message" => "Record added."]);
                            } else {
                                echo json_encode(["message" => "Error: " . $insCmd->error]);
                            }
                            $insCmd->close();
                        }
                        $dbCon->close();
                    }
                } else {
                    echo json_encode(["message" => "Only Can Access Admin or Staff"]);
                }
                break;
        }
    } else {
        echo ("Bad request!!!!");
    }
}
