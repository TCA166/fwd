<?php
    require "functions.php";
    require_once "dbconnect.php";
    $conn = @new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $id = $_POST["id"];
    $type = $_POST["type"];
    switch($type){
        case 0:
            $sql = "SELECT * FROM pasy WHERE nawierchniaID='$id'";
            $pasy = $conn->query($sql);
            while($pas = $pasy->fetch_assoc()){
                $pasID = $pas["ID"];
                $sql = "UPDATE sekcje SET pasID=NULL WHERE pasID='$pasID'";
                if ($conn->query($sql) !== TRUE) {
                    die("Error updating record: " . $conn->error);
                }
                $sql = "DELETE FROM pasy WHERE ID='$pasID'";
                if ($conn->query($sql) !== TRUE) {
                    die("Error deleting record: " . $conn->error);
                }
            }
            $sql = "DELETE FROM nawierzchnie WHERE ID='$id'";
            if ($conn->query($sql) !== TRUE) {
                die("Error deleting record: " . $conn->error);
            }
            break;
        case 1:
            $sql = "SELECT * FROM pasy WHERE ID='$id'";
            $pasy = $conn->query($sql);
            $pas = $pasy->fetch_assoc();
            $pasID = $pas["ID"];
            $sql = "UPDATE sekcje SET pasID=NULL WHERE pasID='$pasID'";
            if ($conn->query($sql) !== TRUE) {
                die("Error updating record: " . $conn->error);
            }
            $sql = "DELETE FROM pasy WHERE ID='$pasID'";
            if ($conn->query($sql) !== TRUE) {
                die("Error deleting record: " . $conn->error);
            }
            break;
        case 2:
            $sql = "UPDATE sekcje SET pasID=NULL WHERE filename='$id'";
            if ($conn->query($sql) !== TRUE) {
                die("Error updating record: " . $conn->error);
            }
            break;
    }

?>