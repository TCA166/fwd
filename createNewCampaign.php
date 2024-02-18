<?php //script for adding a new row to the sesje table 
    $sessionName = $_POST["name"];
    $date = $_POST["date"];
    require_once "dbconnect.php";
    //Connect to db
    $conn = @new mysqli($host, $user, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $sql = "INSERT INTO sesje VALUES('$sessionName','$date',UUID())";
    if ($conn->query($sql) !== TRUE) {
        die("Error creating record: " . $conn->error);
    }
    $conn->commit();
    $conn->close();
    header("Location: http://sump-osad.pl/osad_FWD-Dynatest/manage.php");
    die();
?>