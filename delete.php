<?php //script for deleting a sesja row
    require "functions.php";
    require_once "dbconnect.php";
    $session = $_POST["sessionSelect"];
    echo $session;
    //we need to sanitise input to prevent Sqlinjection
    $session = sanitise($session);
    //Connect to db
    $conn = @new mysqli($host, $user, $password, $dbname);
    //delete the sections associated with this campaign
    $sql = "SELECT * FROM sekcje WHERE sesjaID='$session'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $path = realpath("./fwdData/") . "/" . $row["filename"] . ".json";
            if(!unlink($path)){
                die("Couldn't delete the associated json file"  . $row["filename"]);
            }
        }
    }
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "DELETE FROM sesje WHERE ID='$session'";
    if ($conn->query($sql) !== TRUE) {
        die("Error deleting record: " . $conn->error);
    }
    $sql = "DELETE FROM sekcje WHERE sesjaID='$session'";
    if ($conn->query($sql) !== TRUE) {
        die("Error deleting record: " . $conn->error);
    }
    $conn->commit();
    $conn->close();
    header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
    die();
?>