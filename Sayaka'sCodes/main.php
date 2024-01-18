<?php
header("Access-Control-Allow-Origin: *");
require("./config.php");

// check if the request method is post
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_SERVER["PATH_INFO"])) {
        // If a user is logged in, check for session timeout and redirect to the login page if inactive
        if (isset($_POST["sid"])) {
            session_id($_POST["sid"]);
            session_start();
            // Check if the last activity time is set
            if (isset($_SESSION["last_activity"])) {
                $timeout_duration = 600; // 10 minutes
                $inactive_duration = time() - $_SESSION["last_activity"];
                // Check if the user has been inactive for more than the specified timeout duration
                if ($inactive_duration >= $timeout_duration) {
                    // Session has timed out, destroy the session and redirect to login
                    session_unset();
                    session_destroy();
                } else {
                    // Update the last activity timestamp
                    $_SESSION["last_activity"] = time() + 600;
                }
            }
        }

        switch($_SERVER["PATH_INFO"]) {
            case "/login":
                $loginUser = null;
                $flag = true; // to check if the user type is staff
                $dbCon = mysqli_connect($dbServer,$dbUser,$dbPass,$dbName);
                if(!$dbCon) {
                    die("Connection error" .mysqli_connect_error());
                } else {
                    // Query to check if the entered email and user type match a record in the database
                    $result = mysqli_query($dbCon, "SELECT * FROM user_tb WHERE email='" . $_POST["email"] . "' AND type='" . $_POST["type"] . "'");
                    // Fetch the result as an associative array
                    $user = mysqli_fetch_array($result);
                    if($user > 0) { // if the user data exist on the database
                        if($_POST["type"] === "Staff") { // if the user data is staff
                            // check if the user data exists on the approval table
                            $resultAppr = mysqli_query($dbCon,"SELECT * FROM approval_tb WHERE uid = " . $user["uid"]);
                            if(mysqli_num_rows($resultAppr) > 0) { // if the user data is pending, the user can't log in.
                                $flag = false;
                            }
                        }
                        if($flag) { // if the user type is not staff or the staff data is already approved
                            // check if the user data is in the black list
                            $resultBlk = mysqli_query($dbCon, "SELECT * FROM blacklst_tb WHERE uid = " . $user ["uid"]);
                            if(mysqli_num_rows($resultBlk) > 0) { // if the user data exists in the black list, the user can't login.
                                $loginUser = 0;
                                echo "Account is locked due to too many unsuccessful login attempts. Please try again later.";
                            } else {
                                // Verify the enterd password and the hashed password on the user table
                                if (password_verify($_POST["pass"],$user["pass"])) {
                                    if($user["ecount"] != 5) {
                                        // Password is correct, reset login attempts
                                        mysqli_query($dbCon, "UPDATE user_tb SET ecount = 5 WHERE uid=" . $user["uid"]);
                                    }
                                    // Set session variables for logged in user, and set timestamp for the last activity (login time)
                                    session_start();
                                    $_SESSION["loginUser"] = $user;
                                    $_SESSION["last_activity"] = time() + 600;
                                } else {
                                    $user["ecount"]--; // reduce the error count of password
                                    if($user["ecount"] <= 0) {//Lock the user account after unsuccessful authentication attempts passes 5 times.
                                        mysqli_query($dbCon,"INSERT INTO blacklst_tb (uid) VALUES (".$user["uid"] .")");
                                    } 
                                    // update the ecount on the user table 
                                    mysqli_query($dbCon, "UPDATE user_tb SET ecount=".$user["ecount"]." WHERE uid=".$user["uid"]);
                                }
                            }
                        }
                    }
                }
                if(session_status() === 2){ // if session works, return the user type and session id to the front-end
                    $response = ["type"=>$_SESSION["loginUser"]["type"], "sid"=>session_id()];
                    echo json_encode($response);
                } else if($loginUser === null && $flag === false) { // if the user type is staff and the data isn't approved
                    echo "Your data is not approved!";
                } else if($loginUser === null){ // if the email/password/type is wrong.
                    echo "email/password/type is wrong.";
                }
                mysqli_close($dbCon);
            break;

            case "/logout":
                if(isset($_SESSION["loginUser"])){ // if the user is logged in, stop session
                    session_unset();
                    session_destroy();
                    echo "Log out";
                }else{ 
                    echo "Login first.";
                }
            break;

            case "/reg":
                $dbCon = mysqli_connect($dbServer,$dbUser,$dbPass,$dbName);
                if(!$dbCon){
                    die("Connection to DB failed! ".mysqli_connect_error());
                }
                $selectCmd = "SELECT email FROM user_tb WHERE email='".$_POST["email"]."'";
                $result = $dbCon->query($selectCmd);
                if($result->num_rows > 0){
                    echo "Registration failed!";
                    $dbCon->close();
                }else{
                    $insCmd = $dbCon->prepare("INSERT INTO user_tb (fname,lname,email,pass,type) VALUES (?,?,?,?,?)");
                    $insCmd->bind_param("sssss",$_POST["fname"],$_POST["lname"],$_POST["email"],password_hash($_POST["pass"],PASSWORD_BCRYPT,["cost"=>10]),$_POST["type"]);
                    $insCmd->execute();
                    echo "Record added.";
                    $insCmd->close();
                    $dbCon->close();
                }
            break;
            
            // load the staff data which aren't approved yet
            case "/alist":
                if(isset($_SESSION["loginUser"])) { // if a user log in, start to connect to the approval table
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
                if(isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["type"] === "Admin") { 
                    if (isset($_POST['approve'])) {
                        $staff = json_decode($_POST['approve'],true); // the user data which will be approved. it's converted to php object 
                        $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                        if($dbCon->connect_error){
                            echo "DB connection error. ".$dbCon->connect_error;
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
                if(isset($_SESSION["loginUser"])) { // if the user s logged in, start to connect to the book table
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    if($dbCon->connect_error){
                        echo "DB connection error. ".$dbCon->connect_error;
                        $dbCon->close();
                    } else {
                        $loadBlist = "SELECT * FROM books_tb"; // load all of the data in the book table
                        $result = $dbCon->query($loadBlist);
                        if($result->num_rows > 0) { // if the table has data, load each book data
                            $blist = [];
                            while($book = $result->fetch_assoc()){
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
                if(isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["type"] === "Customer") { // if a user log in and the user type is customer, the user can borrow books.
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    if($dbCon->connect_error){
                        echo "DB connection error. ".$dbCon->connect_error;
                        $dbCon->close();
                    } else {
                        $bookBor = json_decode($_POST["book"]);
                        foreach($bookBor as $book) {
                            // insert the data of the books borrowed to the lend table
                            $insLend = $dbCon->prepare("INSERT INTO lend_tb (isbn, uid, ldate,rdata) VALUES (?,?,?,?)");
                            $today = date("Y-m-d");
                            $returnDate  = date("Y-m-d", strtotime($today.'+1 week'));
                            $insLend->bind_param("siss",$book->isbn, $_SESSION["loginUser"]["uid"], $today,$returnDate); 
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
        }
    } else{
        echo("Bad request!!!!");
    }
}
?>