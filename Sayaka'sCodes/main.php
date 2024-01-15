<?php
header("Access-Control-Allow-Origin: *");
require("./config.php");

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_SERVER["PATH_INFO"])) {
        if(isset($_POST["sid"])){
            session_id($_POST["sid"]);
            session_start(); // resume the previous session using the session ID
            if(isset($_SESSION["timeout"]) && $_SESSION["timeout"] > time()) { // still within the threshold
                $_SESSION["timeout"] = time() + 60;
            } else {
                session_unset();
                session_destroy();
            }
        } 

        switch($_SERVER["PATH_INFO"]) {
            // case "/login"://Lock the user account after unsuccessful authentication attempts passes 5 times.
            //     $loginUser = null;
            //     $dbCon = mysqli_connect($dbServer,$dbUser,$dbPass,$dbName);
            //     if(!$dbCon) {
            //         die("Connection error" .mysqli_connect_error());
            //     } else {
            //         $selectCmd = "SELECT * FROM `customers_tb` WHERE email = '" . $_POST["email"] . "'";
            //         $result = mysqli_query($dbCon, $selectCmd);
            //         if(mysqli_num_rows($result) > 0) {
            //             $user = mysqli_fetch_assoc($result);
            //             $selectCid = "SELECT * FROM `blacklst_tb` WHERE uid = " . $user["cid"] ;
            //             $cidResult = mysqli_query($dbCon, $selectCid);
            //             if(mysqli_num_rows($cidResult) > 0) { 
            //                 echo "Account blocked. Ask the support team to unblock.";
            //                 $loginUser = 0;
            //             } else { 
            //                 if($_POST["pass"] === $user["pass"]){ // if the hased password matches user id in the user_tb, success to login 
            //                     if($user["ecount"] != 5) { // if the ecount isn't 5 , reset it to 5
            //                         $update = "UPDATE customers_tb SET ecount = 5 WHERE uid = " .$user["cid"]; // query to reset the ecount to 5 in the user_tb
            //                         mysqli_query($dbCon,$update);
            //                     }
            //                     session_start();
            //                     $_SESSION["loginUser"] = $user;
            //                     $_SESSION["timeout"] = time() + 60;// store the current timestamp of login
            //                 } else {
            //                     $user["ecount"] --; // reduce ecount
            //                     if($user["ecount"] <= 0) { // if ecount is 0, set the user data to the black list
            //                         $insBlack = "INSERT INTO blacklst_tb (uid) VALUES (" .$user["cid"] .")";
            //                         mysqli_query($dbCon,$insBlack);
            //                     } 
            //                     $updateUser = "UPDATE customers_tb SET ecount = " .$user["ecount"]. " WHERE uid = " .$user["cid"];// update ecount
            //                     mysqli_query($dbCon, $updateUser);
            //                 }
            //             } // PHP_SESSION_ACTIVE = 2 means session is enabled and one session exists
            //         }// PHP_SESSION_NONE is 1 means session is enabled but no session exists
            //     }
            //     if(session_status() === 2){
            //         echo session_id();
            //     }else if($loginUser === null){
            //         echo "username/password is wrong.";
            //     }
            //     mysqli_close($dbCon);
            // break;

            case "/blist":
                if(isset($_SESSION["loginUser"])) { // if a user log in, start to connect to the book table
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
                if(isset($_SESSION["loginUser"]) && $_SESSION["loginUser"]["type"] === "customer") { // if a user log in and the user type is customer, the user can borrow books.
                    $dbCon = new mysqli($dbServer, $dbUser, $dbPass, $dbName);
                    if($dbCon->connect_error){
                        echo "DB connection error. ".$dbCon->connect_error;
                        $dbCon->close();
                    } else {
                        $bookBor = json_decode($_POST["book"]);
                        foreach($bookBor as $book) {
                            // insert the data of the books borrowed to the lend table
                            $insLend = $dbCon->prepare("INSERT INTO lend_tb (isbn, cid, ldate) VALUES (?,?,?)");
                            $insLend->bind_param("sis",$book->isbn, $_SESSION["loginUser"]["cid"], date("Y-m-d"));
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