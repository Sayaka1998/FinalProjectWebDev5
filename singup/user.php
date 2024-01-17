<?php
class User {
    private $userid;
    private $name;
    private $email;

    function __construct($userid, $name, $email) {
        $this->name = $name;
        $this->email = $email;
        $this->userid = $userid;
    }

    function displayInfo() {
        return ["userid" => $this->userid, "name" => $this->name, "useremail" => $this->email];
    }
}
?>
